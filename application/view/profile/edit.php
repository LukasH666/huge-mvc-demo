<div class="container">
    <div class="profile-edit-card">
        <h1>Profil bearbeiten</h1>
        <p class="profile-edit-info">
            Hier kannst du zusätzliche Informationen zu deinem Profil speichern.
        </p>

        <?php if (!empty($this->profile->cover_picture_filename)) { ?>
            <div class="cover-picture-preview">
                <img src="<?php echo Config::get('URL'); ?>profile/cover/<?php echo $this->profile->user_id; ?>" alt="Titelbild">
            </div>
        <?php } ?>

        <?php if (!empty($this->profile->profile_picture_filename)) { ?>
            <div class="profile-picture-preview">
                <img src="<?php echo Config::get('URL'); ?>profile/picture/<?php echo $this->profile->user_id; ?>" alt="Profilbild">
            </div>
        <?php } ?>

        <form action="<?php echo Config::get('URL'); ?>profile/save" method="post" enctype="multipart/form-data" class="profile-edit-form">

            <div class="profile-form-group">
                <label for="cover_picture">Titelbild</label>
                <input type="file" name="cover_picture" id="cover_picture" accept="image/jpeg,image/png,image/gif">
                <small>Erlaubt: JPG, PNG, GIF. Maximal 5 MB.</small>
            </div>

            <div class="profile-form-group">
                <label for="profile_picture">Profilbild</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg,image/png,image/gif">
                <small>Erlaubt: JPG, PNG, GIF. Maximal 3 MB.</small>
            </div>

            <div class="profile-form-group">
                <label for="bio">Beschreibung / Bio</label>
                <textarea name="bio" id="bio" rows="5"><?php echo htmlspecialchars($this->profile->bio); ?></textarea>
            </div>

            <div class="profile-form-group">
                <label for="location">Wohnort</label>
                <input type="text" name="location" id="location"
                       value="<?php echo htmlspecialchars($this->profile->location); ?>">
            </div>

            <div class="profile-form-group">
                <label for="hobby">Hobby</label>
                <input type="text" name="hobby" id="hobby"
                       value="<?php echo htmlspecialchars($this->profile->hobby); ?>">
            </div>

            <div class="profile-form-group">
                <label for="birthday">Geburtsdatum</label>
                <input type="date" name="birthday" id="birthday"
                       value="<?php echo htmlspecialchars($this->profile->birthday); ?>">
            </div>

            <input type="submit" value="Profil speichern" class="profile-save-button">
        </form>
        <hr>

        <div class="profile-post-create-card">
            <h2>Neuer Beitrag</h2>
            <p class="profile-edit-info">
                Lade ein Bild hoch und schreibe optional eine kurze Beschreibung dazu.
            </p>

            <form action="<?php echo Config::get('URL'); ?>profilePost/create"
                  method="post"
                  enctype="multipart/form-data"
                  class="profile-edit-form">

                <div class="profile-form-group">
                    <label for="post_image">Bild</label>
                    <input type="file" name="post_image" id="post_image" accept="image/jpeg,image/png,image/gif" required>
                    <small>Erlaubt: JPG, PNG, GIF. Maximal 5 MB.</small>
                </div>

                <div class="profile-form-group">
                    <label for="caption">Caption</label>
                    <textarea name="caption" id="caption" rows="3" placeholder="Schreibe etwas zu deinem Beitrag..."></textarea>
                </div>

                <input type="submit" value="Beitrag veröffentlichen" class="profile-save-button">
            </form>
        </div>

        <hr>

<div class="profile-post-create-card">
    <h2>Meine Beiträge</h2>

    <?php if (empty($this->posts)) { ?>
        <p class="empty-state">Du hast noch keine Beiträge hochgeladen.</p>
    <?php } else { ?>
        <div class="profile-instagram-grid">
            <?php foreach ($this->posts as $post) { ?>
                <div class="profile-instagram-post">
                    <a href="<?php echo Config::get('URL'); ?>profilePost/show/<?php echo (int)$post->post_id; ?>">
    <img src="<?php echo Config::get('URL'); ?>profilePost/image/<?php echo (int)$post->post_id; ?>" alt="Beitrag">
</a>

                    <div class="profile-post-overlay">
                        <?php if (!empty($post->caption)) { ?>
                            <p><?php echo htmlspecialchars($post->caption); ?></p>
                        <?php } else { ?>
                            <p>Keine Caption vorhanden.</p>
                        <?php } ?>

                        <a class="profile-post-delete-overlay"
                           href="<?php echo Config::get('URL'); ?>profilePost/delete/<?php echo $post->post_id; ?>"
                           onclick="return confirm('Beitrag wirklich löschen?');">
                            Löschen
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
    </div>
</div>