<div class="container">
    <h1>Freunde</h1>

    <div class="box friend-box">

        <?php $this->renderFeedbackMessages(); ?>

        <h3>Offene Freundschaftsanfragen</h3>

        <?php if (empty($this->pendingRequests)) { ?>
            <p class="empty-state">Du hast aktuell keine offenen Freundschaftsanfragen.</p>
        <?php } else { ?>
            <?php foreach ($this->pendingRequests as $request) { ?>
                <div class="friend-request">
                    <strong><?php echo htmlspecialchars($request->user_name); ?></strong>
                    <span><?php echo htmlspecialchars($request->user_email); ?></span>

                    <form method="post" action="<?php echo Config::get('URL'); ?>friend/acceptRequest" class="inline-form">
                        <input type="hidden" name="request_id" value="<?php echo $request->request_id; ?>">
                        <input type="submit" value="Annehmen" class="friend-action-button">
                    </form>

                    <form method="post" action="<?php echo Config::get('URL'); ?>friend/declineRequest" class="inline-form">
                        <input type="hidden" name="request_id" value="<?php echo $request->request_id; ?>">
                        <input type="submit" value="Ablehnen" class="friend-remove-button">
                    </form>
                </div>
            <?php } ?>
        <?php } ?>

        <hr>

        <h3>Meine Freunde</h3>

        <?php if (empty($this->friends)) { ?>
            <p class="empty-state">Du hast aktuell noch keine Freunde hinzugefügt.</p>
        <?php } else { ?>
            <?php foreach ($this->friends as $friend) { ?>
                <div class="friend-request">
                    <strong><?php echo htmlspecialchars($friend->user_name); ?></strong>
                    <span><?php echo htmlspecialchars($friend->user_email); ?></span>

                    <a href="<?php echo Config::get('URL'); ?>profile/showProfile/<?php echo $friend->user_id; ?>" class="friend-link-button">
                        Profil
                    </a>

                    <a href="<?php echo Config::get('URL'); ?>message/index/<?php echo $friend->user_id; ?>" class="friend-link-button">
                        Nachricht
                    </a>

                    <form method="post"
                          action="<?php echo Config::get('URL'); ?>friend/removeFriend"
                          class="inline-form"
                          onsubmit="return confirm('Freundschaft wirklich löschen?');">
                        <input type="hidden" name="user_id" value="<?php echo $friend->user_id; ?>">
                        <input type="submit" value="Entfernen" class="friend-remove-button">
                    </form>
                </div>
            <?php } ?>
        <?php } ?>

        <hr>

        <h3>Gesendete Anfragen</h3>

        <?php if (empty($this->sentRequests)) { ?>
            <p class="empty-state">Du hast aktuell keine offenen gesendeten Anfragen.</p>
        <?php } else { ?>
            <?php foreach ($this->sentRequests as $request) { ?>
                <div class="friend-request">
                    <strong><?php echo htmlspecialchars($request->user_name); ?></strong>
                    <span><?php echo htmlspecialchars($request->user_email); ?></span>
                    <button class="friend-disabled-button" disabled>Anfrage gesendet</button>
                </div>
            <?php } ?>
        <?php } ?>

        <hr>

        <h3>Benutzer finden</h3>

        <table class="overview-table">
            <thead>
                <tr>
                    <td>Benutzername</td>
                    <td>E-Mail</td>
                    <td>Status</td>
                    <td>Aktion</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->users as $user) { ?>
                    <?php $status = $this->statuses[$user->user_id]; ?>

                    <tr>
                        <td><?php echo htmlspecialchars($user->user_name); ?></td>
                        <td><?php echo htmlspecialchars($user->user_email); ?></td>

                        <td>
                            <?php if ($status == 'friends') { ?>
                                Freunde
                            <?php } elseif ($status == 'request_sent') { ?>
                                Anfrage gesendet
                            <?php } elseif ($status == 'request_received') { ?>
                                Anfrage erhalten
                            <?php } else { ?>
                                Keine Verbindung
                            <?php } ?>
                        </td>

                        <td>
                            <?php if ($status == 'none') { ?>
                                <form method="post" action="<?php echo Config::get('URL'); ?>friend/sendRequest">
                                    <input type="hidden" name="receiver_id" value="<?php echo $user->user_id; ?>">
                                    <input type="submit" value="Anfrage senden" class="friend-action-button">
                                </form>
                            <?php } elseif ($status == 'friends') { ?>
                                <a href="<?php echo Config::get('URL'); ?>message/index/<?php echo $user->user_id; ?>" class="friend-link-button">
                                    Nachricht
                                </a>
                            <?php } elseif ($status == 'request_sent') { ?>
                                <button class="friend-disabled-button" disabled>Anfrage gesendet</button>
                            <?php } elseif ($status == 'request_received') { ?>
                                <span>Bitte oben beantworten</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>