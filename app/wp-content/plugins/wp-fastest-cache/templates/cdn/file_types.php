<div class="wpfc-checkbox-list">
	<?php
		$types = array("aac",
                        "avif",
                        "css",
                        "eot",
                        "gif",
                        "jpeg",
                        "js",
                        "jpg",
                        "less",
                        "mp3",
                        "mp4",
                        "ogg",
                        "otf",
                        "pdf",
                        "png",
                        "svg",
                        "swf",
                        "ttf",
                        "webm",
                        "webp",
                        "woff",
                        "woff2"
                        );

        foreach ($types as $key => $value) {
            ?>
            <label for="file-type-<?php echo $value; ?>">
                <input id="file-type-<?php echo $value; ?>" type="checkbox" value="<?php echo $value; ?>" checked=""><span class="">*.<?php echo $value; ?></span>
            </label>
            <?php
        }
	?>
</div>