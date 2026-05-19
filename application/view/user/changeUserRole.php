<div class="container">
    <h1>Change User Role</h1>

    <div class="box">
        <?php $this->renderFeedbackMessages(); ?>

        <form method="post" action="<?php echo Config::get('URL'); ?>user/changeUserRole_action">
            <label>User auswählen:</label>
            <select name="user_id" required>
                <?php foreach ($this->users as $user) { ?>
                    <option value="<?php echo $user->user_id; ?>">
                        <?php echo htmlspecialchars($user->user_name); ?>
                        -
                        <?php echo htmlspecialchars($user->user_email); ?>
                        aktuell:
                        <?php echo htmlspecialchars($user->group_name); ?>
                    </option>
                <?php } ?>
            </select>

            <br><br>

            <label>Neue Role:</label>
            <select name="user_account_type" required>
                <?php foreach ($this->groups as $group) { ?>
                    <option value="<?php echo $group->group_id; ?>">
                        <?php echo $group->group_id; ?> -
                        <?php echo htmlspecialchars($group->group_name); ?>
                    </option>
                <?php } ?>
            </select>

            <br><br>

            <input type="submit" value="Role ändern">
        </form>
    </div>
</div>