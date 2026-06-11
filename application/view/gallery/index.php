<div class="container">
    <h1>Meine Bildergalerie</h1>

    <div class="box">
        <h3>Bild hochladen</h3>

        <form action="<?php echo Config::get('URL'); ?>gallery/upload" method="post" enctype="multipart/form-data">
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif" required>

            <br><br>

<label>Bildname:</label>
<input type="text" name="image_name" required>
            <input type="submit" value="Hochladen">
        </form>

        <h3>Meine Bilder</h3>

        <?php if (empty($this->files)) { ?>
            <p>Es wurden noch keine Bilder hochgeladen.</p>
        <?php } else { ?>
<div class="gallery-grid">
    <?php foreach ($this->files as $file) { ?>
        <div class="gallery-item">
            <img src="<?php echo Config::get('URL'); ?>gallery/show/<?php echo rawurlencode($file); ?>"
                 alt="<?php echo htmlspecialchars($file); ?>">

            <p><?php echo htmlspecialchars($file); ?></p>

            <a href="<?php echo Config::get('URL'); ?>gallery/download/<?php echo rawurlencode($file); ?>">
                Download
            </a>

            <br>

<a href="<?php echo Config::get('URL'); ?>gallery/delete?file=<?php echo rawurlencode($file); ?>"
   onclick="return confirm('Bild wirklich löschen?');">
    Löschen
</a>
        </div>
    <?php } ?>
</div>
        <?php } ?>
    </div>
</div>