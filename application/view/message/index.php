<div class="container">
    <h1>Messenger</h1>

    <div class="box messenger">

        <div class="messenger-users">
            <h3>Benutzer</h3>

            <?php foreach ($this->users as $user) { ?>
                <?php $unreadFromUser = MessageModel::countUnreadFromUser($user->user_id); ?>

                <p>
                    <a href="<?php echo Config::get('URL'); ?>message/index/<?php echo (int)$user->user_id; ?>">
                        <?php echo htmlspecialchars($user->user_name); ?>

                        <?php if ($unreadFromUser > 0) { ?>
                            <span class="badge"><?php echo $unreadFromUser; ?></span>
                        <?php } ?>
                    </a>
                </p>
            <?php } ?>

            <h3>Gruppen</h3>

            <?php foreach ($this->groups as $group) { ?>
                <?php $unreadGroupMessages = MessageModel::countUnreadGroupMessages($group->group_id); ?>

                <p>
                    <a href="<?php echo Config::get('URL'); ?>message/group/<?php echo (int)$group->group_id; ?>">
                        <?php echo htmlspecialchars($group->group_name); ?>

                        <?php if ($unreadGroupMessages > 0) { ?>
                            <span class="badge"><?php echo $unreadGroupMessages; ?></span>
                        <?php } ?>
                    </a>
                </p>
            <?php } ?>

            <h3>Neue Gruppe</h3>

            <form method="post" action="<?php echo Config::get('URL'); ?>message/createGroup">
                <input type="text"
                       name="group_name"
                       placeholder="Gruppenname"
                       required>

                <br><br>

                <?php foreach ($this->users as $user) { ?>
                    <label>
                        <input type="checkbox"
                               name="members[]"
                               value="<?php echo (int)$user->user_id; ?>">

                        <?php echo htmlspecialchars($user->user_name); ?>
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

                        <div class="<?php echo $ownMessage ? 'msg-own' : 'msg-other'; ?>">

                            <?php if ($this->chat_type == 'group' && !$ownMessage) { ?>
                                <strong><?php echo htmlspecialchars($message->user_name); ?></strong>
                                <br>
                            <?php } ?>

                            <?php if (!empty($message->message_text)) { ?>
                                <?php echo nl2br(htmlspecialchars($message->message_text)); ?>
                                <br>
                            <?php } ?>

                            <?php if ($this->chat_type == 'user' && !empty($message->file_filename)) { ?>
                                <div class="message-attachment">
                                    <?php if (strpos($message->file_mime_type, 'image/') === 0) { ?>
                                        <a href="<?php echo Config::get('URL'); ?>message/download/<?php echo (int)$message->message_id; ?>" target="_blank">
                                            <img src="<?php echo Config::get('URL'); ?>message/download/<?php echo (int)$message->message_id; ?>" alt="Anhang">
                                        </a>
                                    <?php } else { ?>
                                        <a class="message-file-link"
                                           href="<?php echo Config::get('URL'); ?>message/download/<?php echo (int)$message->message_id; ?>"
                                           target="_blank">
                                            Datei öffnen: <?php echo htmlspecialchars($message->file_original_name); ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <small><?php echo htmlspecialchars($message->created_at); ?></small>
                        </div>
                    <?php } ?>
                </div>

                <?php if ($this->chat_type == 'group') { ?>

                    <form method="post"
                          action="<?php echo Config::get('URL'); ?>message/sendGroup/<?php echo (int)$this->group_id; ?>">

                        <textarea name="message_text" required></textarea>
                        <br>
                        <input type="submit" value="Senden">
                    </form>

                <?php } else { ?>

                    <form method="post"
                          action="<?php echo Config::get('URL'); ?>message/send/<?php echo (int)$this->partner_id; ?>"
                          enctype="multipart/form-data">

                        <textarea name="message_text"></textarea>

                        <div class="message-file-upload">
                            <label for="message_file">Datei oder Bild anhängen:</label>
                            <input type="file"
                                   name="message_file"
                                   id="message_file"
                                   accept="image/jpeg,image/png,image/gif,application/pdf,text/plain,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        </div>

                        <input type="submit" value="Senden">
                    </form>

                <?php } ?>

            <?php } ?>
        </div>

    </div>
</div>