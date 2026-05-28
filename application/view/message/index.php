<div class="container">
    <h1>Messenger</h1>

    <div class="box messenger">

        <div class="messenger-users">
            <h3>Benutzer</h3>

<?php foreach ($this->users as $user) { ?>
    <?php $unreadFromUser = MessageModel::countUnreadFromUser($user->user_id); ?>

    <p>
        <a href="<?= Config::get('URL'); ?>message/index/<?= $user->user_id; ?>">
            <?= htmlspecialchars($user->user_name); ?>

            <?php if ($unreadFromUser > 0) { ?>
                <span class="badge"><?= $unreadFromUser; ?></span>
            <?php } ?>
        </a>
    </p>
<?php } ?>
        </div>

        <div class="messenger-chat">
            <?php if (!$this->partner_id) { ?>
                <p>Bitte Benutzer auswählen.</p>
            <?php } else { ?>

                <div class="messages">
                    <?php foreach ($this->messages as $message) { ?>
                        <div class="<?= ($message->sender_id == Session::get('user_id')) ? 'msg-own' : 'msg-other'; ?>">
                            <?= nl2br(htmlspecialchars($message->message_text)); ?>
                            <br>
                            <small><?= $message->created_at; ?></small>
                        </div>
                    <?php } ?>
                </div>

                <form method="post" action="<?= Config::get('URL'); ?>message/send/<?= $this->partner_id; ?>">
                    <textarea name="message_text" required></textarea>
                    <br>
                    <input type="submit" value="Senden">
                </form>

            <?php } ?>
        </div>

    </div>
</div>