<div class="container">
    <div class="profile-post-detail-card">

        <?php $this->renderFeedbackMessages(); ?>

        <div class="profile-post-detail-header">
            <div class="profile-post-author">
                <a class="profile-post-author-image-link"
                   href="<?php echo Config::get('URL'); ?>profile/showProfile/<?php echo (int)$this->user->user_id; ?>">
                    <div class="profile-post-author-image">
                        <?php if (!empty($this->profile->profile_picture_filename)) { ?>
                            <img src="<?php echo Config::get('URL'); ?>profile/picture/<?php echo (int)$this->user->user_id; ?>" alt="Profilbild">
                        <?php } elseif (!empty($this->user->user_avatar_link)) { ?>
                            <img src="<?php echo htmlspecialchars($this->user->user_avatar_link); ?>" alt="Avatar">
                        <?php } else { ?>
                            <div class="profile-post-author-placeholder">?</div>
                        <?php } ?>
                    </div>
                </a>

                <div class="profile-post-author-info">
                    <h1>
                        <a class="profile-post-author-link"
                           href="<?php echo Config::get('URL'); ?>profile/showProfile/<?php echo (int)$this->user->user_id; ?>">
                            <?php echo htmlspecialchars($this->user->user_name); ?>
                        </a>
                    </h1>
                    <p><?php echo htmlspecialchars($this->user->user_email); ?></p>
                </div>
            </div>

            <a class="profile-back-button"
               href="<?php echo Config::get('URL'); ?>profile/showProfile/<?php echo (int)$this->user->user_id; ?>">
                Zurück zum Profil
            </a>
        </div>

        <div class="profile-post-detail-image">
            <img src="<?php echo Config::get('URL'); ?>profilePost/image/<?php echo (int)$this->post->post_id; ?>" alt="Beitrag">
        </div>

        <div class="profile-post-detail-info">
            <?php if (!empty($this->post->caption)) { ?>
                <p class="profile-post-detail-caption">
                    <strong><?php echo htmlspecialchars($this->user->user_name); ?>:</strong>
                    <?php echo nl2br(htmlspecialchars($this->post->caption)); ?>
                </p>
            <?php } else { ?>
                <p class="profile-post-detail-caption empty-caption">
                    <strong><?php echo htmlspecialchars($this->user->user_name); ?>:</strong>
                    Keine Caption vorhanden.
                </p>
            <?php } ?>

            <small>Veröffentlicht am: <?php echo htmlspecialchars($this->post->created_at); ?></small>
        </div>

        <div class="profile-post-comments">
            <h3>Kommentare (<?php echo count($this->comments); ?>)</h3>

            <?php if (empty($this->comments)) { ?>
                <p class="empty-state">Noch keine Kommentare vorhanden.</p>
            <?php } else { ?>
                <div class="profile-comment-list">
                    <?php foreach ($this->comments as $comment) { ?>
                        <div class="profile-comment">
                            <div class="profile-comment-content">
                                <strong><?php echo htmlspecialchars($comment->user_name); ?>:</strong>
                                <?php echo nl2br(htmlspecialchars($comment->comment_text)); ?>
                                <br>
                                <small><?php echo htmlspecialchars($comment->created_at); ?></small>
                            </div>

                            <?php if ($comment->user_id == Session::get('user_id') || $this->post->user_id == Session::get('user_id')) { ?>
                                <a class="profile-comment-delete"
                                   href="<?php echo Config::get('URL'); ?>profilePost/deleteComment/<?php echo (int)$comment->comment_id; ?>"
                                   onclick="return confirm('Kommentar wirklich löschen?');">
                                    Löschen
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <form method="post"
                  action="<?php echo Config::get('URL'); ?>profilePost/addComment/<?php echo (int)$this->post->post_id; ?>"
                  class="profile-comment-form">

                <textarea name="comment_text"
                          rows="3"
                          placeholder="Kommentar schreiben..."
                          required></textarea>

                <br>

                <input type="submit" value="Kommentieren" class="profile-save-button">
            </form>
        </div>

        <?php if ($this->post->user_id == Session::get('user_id')) { ?>
            <div class="profile-post-detail-actions">
                <a class="profile-post-delete"
                   href="<?php echo Config::get('URL'); ?>profilePost/delete/<?php echo (int)$this->post->post_id; ?>"
                   onclick="return confirm('Beitrag wirklich löschen?');">
                    Beitrag löschen
                </a>
            </div>
        <?php } ?>

    </div>
</div>