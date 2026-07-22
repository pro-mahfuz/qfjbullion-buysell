/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 * 
 */

"use strict";

$(function () {
    var fileupload = $("#FileUpload1");
    var filePath = $("#spnFilePath");
    var image = $("#imgFileUpload");
    image.click(function () {
        fileupload.click();
    });
    fileupload.change(function () {
        var fileName = $(this).val().split('\\')[$(this).val().split('\\').length - 1];
        filePath.html("<b>Selected File: </b>" + fileName);
        var output = document.getElementById('imgFileUpload');
        output.src = URL.createObjectURL(event.target.files[0]);
    });
});



