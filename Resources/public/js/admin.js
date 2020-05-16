(function () {
    function file(value, _a) {
        var name = _a[0], _b = _a[1], options = _b === void 0 ? {} : _b;
        var output = 'Файл не загружен';

        options = $.extend({deletable: true}, options);

        if (value) {
            output = filePreview(value);
        }

        return "<div class=\"panel panel-default media-file\"><div class=\"panel-heading\">" +
            ((value && options.deletable) ? '<div class="pull-right"><label class="small"><input type="checkbox" name="' + name + '__delete"> Очистить поле</label></div>' : '') +
            "<input type=\"file\" name=\"" + name + "\"></div><div class=\"panel-body\">" + output + "</div></div>";
    }

    function filePreview(value) {
        if (value) {
            var output = '<div class="media media-file">';

            if (value.image_url) {
                output += '<div class="media-left"><div class="thumbnail"><a href="' + value.url + '" target="_blank"><img src="' + value.image_url + '" alt="' + value.original_name + '"></a></div></div>';
            }

            output += '<div class="media-body"><a href="' + value.url + '" target="_blank">' + value.original_name + '</a> (' + value.size + ')</div>';
            output += '</div>';

            return output;
        }

        return '';
    }

    function imagePreview(value) {
        if (value) {
            var output = '<div class="media media-file">';

            if (value.image_url) {
                output += '<div class="media-left"><div class="thumbnail"><a href="' + value.url + '" target="_blank"><img src="' + value.image_url + '" alt="' + value.original_name + '"></a></div></div>';
            }
            output += '</div>';

            return output;
        }

        return '';
    }

    function video(value, _a) {
        var _b = _a === void 0 ? ['', {}] : _a, name = _b[0], _c = _b[1], options = _c === void 0 ? {} : _c;
        var output = 'Видео не загружено', source = '';
        if (value) {
            source = value.source;
            output = videoPreview(value);
        }
        return "<div class=\"panel panel-default media-video\"><div class=\"panel-heading\"><input type=\"text\" class=\"form-control\" name=\"" + name + "\" value=\"" + source + "\" placeholder=\"Источник видео\"></div><div class=\"panel-body\">" + output + "</div></div>";
    }

    function videoPreview(value) {
        return videoPreviewImage(value) || videoPreviewEmbed(value) || 'Видео загружено. Предпросмотр видео недоступен';
    }

    function videoPreviewEmbed(value) {
        if (value.embed_url) {
            return '<div class="media media-video"><div class="media-left"><div class="thumbnail"><iframe allowfullscreen frameborder="0" src="' + value.embed_url + '"></iframe></div></div></div>';
        }

        return '';
    }

    function videoPreviewImage(value) {
        if (value.image_url) {
            return '<div class="media media-video"><div class="media-left"><div class="thumbnail"><img src="' + value.image_url + '" alt=""></div></div></div>';
        }

        return '';
    }

    function gallery(value, options) {
        var name = options && options[0] ? options[0] : '', output = 'Изображение не загружено';
        var video, image;

        video = !(options[1] && !options[1].video);
        image = !(options[1] && !options[1].image);

        return Creonit.Admin.Component.Helpers.component('Media.GalleryTable', {
            field_name: name,
            gallery_id: value,
            image: image,
            video: video
        }, {}) + ("<input type=\"hidden\" name=\"" + name + "\" value=\"" + value + "\">");
    }

    Creonit.Admin.Component.Helpers.file = file;
    Creonit.Admin.Component.Helpers.video = video;
    Creonit.Admin.Component.Helpers.gallery = gallery;

    Creonit.Admin.Component.Helpers.filePreview = filePreview;
    Creonit.Admin.Component.Helpers.imagePreview = imagePreview;
    Creonit.Admin.Component.Helpers.videoPreview = videoPreview;
    Creonit.Admin.Component.Helpers.videoPreviewImage = videoPreviewImage;
    Creonit.Admin.Component.Helpers.videoPreviewEmbed = videoPreviewEmbed;

    Creonit.Admin.Component.Helpers.registerTwigFilter('file', file);
    Creonit.Admin.Component.Helpers.registerTwigFilter('image', file);
    Creonit.Admin.Component.Helpers.registerTwigFilter('video', video);
    Creonit.Admin.Component.Helpers.registerTwigFilter('gallery', gallery);

    Creonit.Admin.Component.Helpers.registerTwigFilter('file_preview', filePreview);
    Creonit.Admin.Component.Helpers.registerTwigFilter('image_preview', imagePreview);
    Creonit.Admin.Component.Helpers.registerTwigFilter('video_preview', videoPreview);
    Creonit.Admin.Component.Helpers.registerTwigFilter('video_preview_image', videoPreviewImage);
    Creonit.Admin.Component.Helpers.registerTwigFilter('video_preview_embed', videoPreviewEmbed);
})();