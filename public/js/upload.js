/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function fileSelect(evt) {
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        var files = $('#pictures')[0].files;
        var result = '';
        var file;
        for (var i = 0; file = files[i]; i++) {
            result += '<li><span class="label label-default">' + file.name +'</span></li>';
        }
        document.getElementById('filesInfo').innerHTML = '<ul class="list-unstyled">' + result + '</ul>';
    } else {
        alert('The File APIs are not fully supported in this browser.');
    }
}

document.getElementById('pictures').addEventListener('change', fileSelect, false);
if (window.File && window.FileReader && window.FileList && window.Blob) {
    document.getElementById('submit').onclick = function(event) {
        event.preventDefault();
        var date = new Date();
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        var hour = date.getHours();
        var minute = date.getMinutes();
        var second = date.getSeconds();
        if (day < 10) {
            day = '0' + day;
        }
        if (month < 10) {
            month = '0' + month;
        }
        if (hour < 10) {
            hour = '0' + hour;
        }
        if (minute < 10) {
            minute = '0' + minute;
        }
        if (second < 10) {
            second = '0' + second;
        }
        var dateStr = day + month + year + '_' + hour + minute + second;
        var files = document.getElementById('pictures').files;
        var plate = document.getElementById('plate').value;
        var message = document.getElementById('message').value;
        if (message !== '') {
            uploadIncidence(plate, dateStr, message);
            for (var i = 0; i < files.length; i++) {
                resizeAndUpload(files[i], dateStr, plate);
            }
        } else {
            $('#textInfo').prepend('<div class="alert alert-warning alert-dismissable">' +
  '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
  'El campo incidencia es obligatorio' +
'</div>');
        }
        //window.location.href = "http://cliente2_0.dev/clients/listOts/";
    };
} else {
    alert('The File APIs are not fully supported in this browser.');
}

function uploadIncidence(plate, date, message) {
    var ajaxData = {
        "plate": plate,
        "date": date,
        "message": message
    };
    $.ajax({
        url: 'http://195.57.212.155/clients/uploadIncidenceAjax/',
        data: ajaxData,
        async: true,
        type: "get",
        success: function(json) {
            var errno = json.substring(0, 1);
            json = json.substring(1);
            if (errno === '0') {
                $('#textInfo').prepend($('<div class="alert alert-success"><span '
                        + 'class="glyphicon glyphicon-ok"></span> '
                        + 'Incidencia subida con éxito</div>').hide());
                $('#textInfo .alert').fadeIn(500);
            } else {
                $('#textInfo').prepend($('<div class="alert alert-danger"><span '
                        + 'class="glyphicon glyphicon-remove"></span> '
                        + json + '</div>').hide());
                $('#textInfo .alert').fadeIn(500);
            }
        },
        error: function(jQHR, textStatus, thrownError) {
            $('#textInfo').prepend($('<div class="alert alert-danger"><span '
                    + 'class="glyphicon glyphicon-remove"></span> '
                    + 'Error en la subida de la incidencia</div>').hide());
            $('#textInfo .alert').fadeIn(500);
        }
    });
}

function resizeAndUpload(file, date, plate) {
    var reader = new FileReader();
    reader.onloadend = function() {
        var tempImg = new Image();
        tempImg.src = reader.result;
        tempImg.onload = function() {
            var MAX_WIDTH = 1024;
            var MAX_HEIGHT = 768;
            var tempW = tempImg.width;
            var tempH = tempImg.height;
            if (tempW > tempH) {
                if (tempW > MAX_WIDTH) {
                    tempH *= MAX_WIDTH / tempW;
                    tempW = MAX_WIDTH;
                }
            } else {
                if (tempH > MAX_HEIGHT) {
                    tempW *= MAX_HEIGHT / tempH;
                    tempH = MAX_HEIGHT;
                }
            }

            var canvas = document.createElement('canvas');
            canvas.width = tempW;
            canvas.height = tempH;
            var ctx = canvas.getContext("2d");
            ctx.drawImage(this, 0, 0, tempW, tempH);
            var dataURL = canvas.toDataURL("image/jpeg");

            var ajaxData = {
                "date": date,
                "plate": plate,
                "imageName": file.name,
                "image": dataURL
            };
            $.ajax({
                url: 'http://195.57.212.155/clients/uploadPhotoAjax/',
                data: ajaxData,
                type: "post",
                async: true,
                beforeSend: function() {
                    $('#filesInfo').html('<img id="loading" src="/images/ajax-loader.gif">');
                },
                success: function(json) {
                    $('#loading').remove();
                    var errno = json.substring(0, 1);
                    json = json.substring(1);
                    if (errno === '0') {
                        $('#filesInfo').prepend($('<div class="alert alert-success"><span '
                                + 'class="glyphicon glyphicon-ok"></span> '
                                + json + '</div>').hide());
                        $('#filesInfo .alert').fadeIn(500);
                    } else {
                        $('#filesInfo').prepend($('<div class="alert alert-danger"><span '
                                + 'class="glyphicon glyphicon-remove"></span> Error: '
                                + json + '</div>').hide());
                        $('#filesInfo .alert').fadeIn(500);
                    }
                },
                error: function(jQHR, textStatus, thrownError) {
                    $('#loading').remove();
                    $('#filesInfo').prepend($('<div class="alert alert-danger"><span '
                            + 'class="glyphicon glyphicon-remove"></span> '
                            + 'Error en la subida de la foto</div>').hide());
                    $('#filesInfo .alert').fadeIn(500);
                }
            });
        };
    };
    reader.readAsDataURL(file);
}
