<?php

require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

function websocketSecret(): string
{
    $secret = getenv('WEBSOCKET_SECRET');
    if ($secret) {
        return $secret;
    }

    $envFile = dirname(__DIR__).'/.env';
    if (is_file($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), 'WEBSOCKET_SECRET=')) {
                $value = trim(substr($line, strlen('WEBSOCKET_SECRET=')), " \t\n\r\0\x0B\"");
                if ($value !== '') return $value;
            }
            if (str_starts_with(trim($line), 'APP_KEY=')) {
                $value = trim(substr($line, strlen('APP_KEY=')), " \t\n\r\0\x0B\"");
                if ($value !== '') $secret = $value;
            }
        }
    }

    return (string) $secret;
}

class ChatServer implements MessageComponentInterface {

    protected $clients;
    protected $users = [];            // resourceId => user_id
    protected $userConnections = [];  // user_id => [connections]

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo "Server started...\n";
    }

    public function onOpen(ConnectionInterface $conn) {

        $this->clients->attach($conn);

        $query = [];
$queryString = $conn->httpRequest->getUri()->getQuery();
parse_str($queryString ?? '', $query);

        $userId = (string) ($query['user_id'] ?? '');
        $expires = (int) ($query['expires'] ?? 0);
        $signature = (string) ($query['signature'] ?? '');
        $expected = hash_hmac('sha256', $userId.'|'.$expires, websocketSecret());

        if ($userId === '' || $expires < time() || $expires > time() + 120 || !hash_equals($expected, $signature)) {
            $conn->close();
            return;
        }

        $this->users[$conn->resourceId] = $userId;
        $this->userConnections[$userId][] = $conn;

        // broadcast online
        $this->broadcast([
            'type' => 'online',
            'user_id' => $userId
        ]);
    }

  public function onMessage(ConnectionInterface $from, $msg) {

    $data = json_decode($msg, true);

    if (!is_array($data)) return;
    $authenticatedUser = $this->users[$from->resourceId] ?? null;
    if (!$authenticatedUser) { $from->close(); return; }
    $data['from'] = $authenticatedUser;
    $type = $data['type'] ?? null;
    $toUser = isset($data['to']) ? (string) $data['to'] : null;


    // ===== call =====

    if(in_array($type, ['offer','answer','ice','live-viewer-join','live-offer','live-answer','live-ice','live-ended'], true)){
        $toUser = $data['to'] ?? null;
        if(!$toUser) return;
        if(isset($this->userConnections[$toUser])){
            foreach ($this->userConnections[$toUser] as $client) {
                $client->send(json_encode($data));
            }
        }
        return;
    }

    // ===== seen =====
    if(isset($data['type']) && $data['type'] === 'seen'){
        $toUser = $data['to'] ?? null;
        if(!$toUser) return;
        $payload = json_encode($data);
        if(isset($this->userConnections[$toUser])){
            foreach ($this->userConnections[$toUser] as $client) {
                $client->send($payload);
            }
        }
        return;
    }

    // ===== typing =====
    if(isset($data['type']) && $data['type'] === 'typing'){
        $toUser = $data['to'] ?? null;
        if(!$toUser) return;
        $payload = json_encode($data);
        if(isset($this->userConnections[$toUser])){
            foreach ($this->userConnections[$toUser] as $client) {
                $client->send($payload);
            }
        }
        return;
    }

    // ===== notification =====
    if(isset($data['type']) && $data['type'] === 'notification'){
        $toUser = $data['to'] ?? null;
        if(!$toUser) return;
        $payload = json_encode($data);
        if(isset($this->userConnections[$toUser])){
            foreach ($this->userConnections[$toUser] as $client) {
                $client->send($payload);
            }
        }
        return;
    }

    // ===== message =====
    if (!$toUser) return;
    $fromUser = $authenticatedUser;
    $payload = json_encode($data);

    foreach (array_unique([$fromUser, $toUser]) as $uid) {

        if (!isset($this->userConnections[$uid])) continue;

        foreach ($this->userConnections[$uid] as $client) {
            $client->send($payload);
        }
    }
}

    public function onClose(ConnectionInterface $conn) {

        $resourceId = $conn->resourceId;
        $userId = $this->users[$resourceId] ?? null;

        $this->clients->detach($conn);

        unset($this->users[$resourceId]);

        if ($userId && isset($this->userConnections[$userId])) {

            // remove connection
            $this->userConnections[$userId] = array_filter(
                $this->userConnections[$userId],
                fn($c) => $c !== $conn
            );

            // لو مفيش connections خالص → user offline
            if (empty($this->userConnections[$userId])) {

                unset($this->userConnections[$userId]);

                $this->broadcast([
                    'type' => 'offline',
                    'user_id' => $userId
                ]);
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        $conn->close();
    }

    private function broadcast($data) {

        $msg = json_encode($data);

        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }
}

/*
|--------------------------------------------------------------------------
| تشغيل السيرفر
|--------------------------------------------------------------------------
*/

$socketPort = (int) (getenv('WEBSOCKET_PORT') ?: 8081);
$socketHost = getenv('WEBSOCKET_HOST') ?: '0.0.0.0';
$loop = React\EventLoop\Loop::get();
$socket = new React\Socket\SocketServer("{$socketHost}:{$socketPort}", [], $loop);
$server = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            new ChatServer()
        )
    ),
    $socket,
    $loop
);

$server->run();
