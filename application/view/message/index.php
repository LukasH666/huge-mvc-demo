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
<h3>Gruppen</h3>

<?php foreach ($this->groups as $group) { ?>
    <?php $unreadGroupMessages = MessageModel::countUnreadGroupMessages($group->group_id); ?>

    <p>
        <a href="<?= Config::get('URL'); ?>message/group/<?= $group->group_id; ?>">
            <?= htmlspecialchars($group->group_name); ?>

            <?php if ($unreadGroupMessages > 0) { ?>
                <span class="badge"><?= $unreadGroupMessages; ?></span>
            <?php } ?>
        </a>
    </p>
<?php } ?>

<h3>Neue Gruppe</h3>

<form method="post" action="<?= Config::get('URL'); ?>message/createGroup">

    <input type="text"
           name="group_name"
           placeholder="Gruppenname"
           required>

    <br><br>

    <?php foreach ($this->users as $user) { ?>

        <label>
            <input type="checkbox"
                   name="members[]"
                   value="<?= $user->user_id; ?>">

            <?= htmlspecialchars($user->user_name); ?>
        </label>

        <br>

    <?php } ?>

    <br>

    <input type="submit" value="Gruppe erstellen">

</form>
        </div>

        <div class="messenger-chat">
<?php if (!$this->partner_id && !$this->group_id) { ?>
    <p>Bitte Benutzer oder Gruppe auswählen.</p>
<?php } else { ?>

                <div class="messages">
                    <?php foreach ($this->messages as $message) { ?>
    <?php $ownMessage = ($message->sender_id == Session::get('user_id')); ?>

    <div class="<?= $ownMessage ? 'msg-own' : 'msg-other'; ?>">

        <?php if ($this->chat_type == 'group' && !$ownMessage) { ?>
            <strong><?= htmlspecialchars($message->user_name); ?></strong>
            <br>
        <?php } ?>

        <?= nl2br(htmlspecialchars($message->message_text)); ?>

        <br>
        <small><?= $message->created_at; ?></small>
    </div>
<?php } ?>
                </div>

<?php if ($this->chat_type == 'group') { ?>
    <form method="post" action="<?= Config::get('URL'); ?>message/sendGroup/<?= $this->group_id; ?>">
<?php } else { ?>
    <form method="post" action="<?= Config::get('URL'); ?>message/send/<?= $this->partner_id; ?>">
<?php } ?>

    <textarea name="message_text" required></textarea>
    <br>
    <input type="submit" value="Senden">
</form>

            <?php } ?>
        </div>

    </div>
</div>