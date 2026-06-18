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
    </div>
</div>