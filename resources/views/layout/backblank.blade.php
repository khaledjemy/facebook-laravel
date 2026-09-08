<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

@include('layout.header')
<body>
    <div id="app" class="app app-header-fixed app-sidebar-fixed  app-with-wide-sidebar">
        <div id="fullspace" class="container-fluid">
                @yield('content')
        </div>
 <!-- Footer-->
        @include('js_')
    </div>
</body>
</html>
<script>
       
$(document).ready(function(){
      var cropper;
  
      $('.make-profile-picture').click(function(e){
          e.preventDefault();
          var userId = $(this).data('user-id');
          var photoId = $(this).data('photo-id');
          var path = @json($photo->path ?? '');
          var type = @json($photo->type ?? '');
          var imageSrc = ASSETS+path+photoId+type;
          $('#image').attr('src', imageSrc); 
          if (cropper) {
              cropper.destroy(); 
          }
          cropper = new Cropper(document.getElementById('image'), {
              aspectRatio: 1, // cercel
              viewMode: 1, // full width in crop
              autoCropArea: 0.9, // defult point crop
              responsive: true, // 
              cropBoxResizable: false, // crop size
              cropBoxWidth: 400, // 
              cropBoxHeight: 400 // 

          })         
            setTimeout(function() {
            $(".cropper-view-box").css({
                "border-radius": "50%", // جعل الزوايا دائرية
                "overflow": "hidden" // إخفاء الزوايا الزائدة
            });
        }, 1000);
           
      });

      $('#cropButton').on('click', function(){
          
          var userId = $(this).data('user-id');
          var photoId = $(this).data('photo-id');
          var path = @json($photo->path ?? '');
          var typee = @json($photo->type ?? '');
          var imageSrc = ASSETS+path+photoId+typee;
          var csrfToken = "{{ csrf_token() }}";
          var croppedCanvas = cropper.getCroppedCanvas();
          if (!croppedCanvas) {
              alert("الرجاء تحديد وقص الصورة أولاً.");
              return;
          }
            var imageData = croppedCanvas.toDataURL("image/jpeg");
            var file = dataURItoBlob(imageData);
            var filee = new File([file], "cropped_image.jpg", { type: "image/jpeg" });

        var formData = new FormData();
            formData.append('croppedImage', filee,'cropped_image.jpg');
            formData.append('userId', userId);
            formData.append('photoId', photoId);
            formData.append('_token', csrfToken);
            formData.append("files", filee,'cropped_image.jpg');
            console.log(formData);
            AjaxReqForAll(ASSETS+"make-profile-picture","POST",formData,'crobed');
            });
            


            $('.make-profile-cover').click(function(e){
                e.preventDefault();
                var userId = $(this).data('user-id');
                var photoId = $(this).data('photo-id');
                var path = @json($photo->path ?? '');
                var type = @json($photo->type ?? '');
                var imageSrc = ASSETS+path+photoId+type;
                $('#imagec').attr('src', imageSrc);
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(document.getElementById('imagec'), {
                    viewMode: 1, 
                    autoCropArea: false, 
                    responsive: true,
                    cropBoxResizable: false, 
                    cropBoxWidth: 700, 
                    cropBoxHeight: 400 
                });
             });
  
      $('#cropButton2').on('click', function(){
          var userId = $(this).data('user-id');
          var photoId = $(this).data('photo-id');
          var path = @json($photo->path ?? '');
          var typee = @json($photo->type ?? '');
          var imageSrc = '../'+path+photoId+typee;
          var csrfToken = "{{ csrf_token() }}";
          var croppedCanvas = cropper.getCroppedCanvas();
          if (!croppedCanvas) {
              alert("الرجاء تحديد وقص الصورة أولاً.");
              return;
          }
            var imageData = croppedCanvas.toDataURL("image/jpeg");
            var file = dataURItoBlob(imageData);
            var filee = new File([file], "cropped_image.jpg", { type: "image/jpeg" });
            var formData = new FormData();
                formData.append('croppedImage', filee,'cropped_image.jpg');
                formData.append('userId', userId);
                formData.append('photoId', photoId);
                formData.append('_token', csrfToken);
                formData.append("files", filee,'cropped_image.jpg');
                formData.append("cover", true);
            $.ajax({
                url: "/make-profile-picture",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('.response-container').html(response);
                    window.location.href=response;
                },
                error: function(xhr, status, error) {
                    console.log("حدث خطأ أثناء معالجة الطلب");
                }
            });
        });
          
            function dataURItoBlob(dataURI) {
                var byteString = atob(dataURI.split(',')[1]);
                var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
                var ab = new ArrayBuffer(byteString.length);
                var ia = new Uint8Array(ab);
                for (var i = 0; i < byteString.length; i++) {
                    ia[i] = byteString.charCodeAt(i);
                }
                var blob = new Blob([ab], {type: mimeString});
                return blob;
                }
            });


    </script>




