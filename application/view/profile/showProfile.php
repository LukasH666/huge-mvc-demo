<div class="container">
    <div class="profile-view-card">

        <?php $this->renderFeedbackMessages(); ?>

        <div class="profile-cover">
            <?php if (!empty($this->profile->cover_picture_filename)) { ?>
                <img src="<?php echo Config::get('URL'); ?>profile/cover/<?php echo htmlspecialchars($this->user->user_id); ?>" alt="Titelbild">
            <?php } else { ?>
                <div class="profile-cover-placeholder"></div>
            <?php } ?>
        </div>

        <div class="profile-view-header">
            <div class="profile-view-image">
                <?php if (!empty($this->profile->profile_picture_filename)) { ?>
                    <img src="<?php echo Config::get('URL'); ?>profile/picture/<?php echo htmlspecialchars($this->user->user_id); ?>" alt="Profilbild">
                <?php } elseif (!empty($this->user->user_avatar_link)) { ?>
                    <img src="<?php echo htmlspecialchars($this->user->user_avatar_link); ?>" alt="Avatar">
                <?php } else { ?>
                    <div class="profile-placeholder">?</div>
                <?php } ?>
            </div>

            <div class="profile-view-title">
                <h1><?php echo htmlspecialchars($this->user->user_name); ?></h1>
                <p><?php echo htmlspecialchars($this->user->user_email); ?></p>
            </div>

<?php if (Session::get('user_id') != $this->user->user_id) { ?>
    <div class="profile-view-actions">
        <a class="profile-message-button"
           href="<?php echo Config::get('URL'); ?>message/index/<?php echo (int)$this->user->user_id; ?>">
            Nachricht senden
        </a>
    </div>
<?php } ?>
        </div>

        <hr>

        <div class="profile-view-section">
            <h3>Über mich</h3>
            <p>
                <?php echo !empty($this->profile->bio) ? nl2br(htmlspecialchars($this->profile->bio)) : 'Keine Beschreibung vorhanden.'; ?>
            </p>
        </div>

        <div class="profile-view-details">
            <div class="profile-view-detail">
                <strong>Wohnort</strong>
                <span><?php echo !empty($this->profile->location) ? htmlspecialchars($this->profile->location) : '-'; ?></span>
            </div>

            <div class="profile-view-detail">
                <strong>Hobby</strong>
                <span><?php echo !empty($this->profile->hobby) ? htmlspecialchars($this->profile->hobby) : '-'; ?></span>
            </div>

            <div class="profile-view-detail">
                <strong>Geburtsdatum</strong>
                <span><?php echo !empty($this->profile->birthday) ? htmlspecialchars($this->profile->birthday) : '-'; ?></span>
            </div>

            <div class="profile-view-detail">
                <strong>Account aktiv</strong>
                <span><?php echo ($this->user->user_active == 1 ? 'Ja' : 'Nein'); ?></span>
            </div>
        </div>

        <br>

        <a class="profile-back-button" href="<?php echo Config::get('URL'); ?>profile/index">Zurück zur Profilübersicht</a>

    </div>
</div>