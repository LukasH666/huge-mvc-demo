<div class="container">
    <h1>Admin/index</h1>

    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>What happens here ?</h3>

        <div>
            This controller/action/view shows a list of all users in the system. with the ability to soft delete a user
            or suspend a user.
        </div>
        <div>
<div style="overflow-x: auto;">
    <table class="overview-table" style="font-size: 12px; width: 100%;">
                <thead>
                <tr>
                    <td>Id</td>
                    <td>Avatar</td>
                    <td>Username</td>
                    <td>User's email</td>
                    <td>Activated ?</td>
                    <td>Role</td>
                    <td>Link to user's profile</td>
                    <td>suspension Time in days</td>
                    <td>Soft delete</td>
                    <td>Submit</td>
                </tr>
                </thead>
                <?php foreach ($this->users as $user) { ?>
    <?php $formId = 'user-form-' . $user->user_id; ?>

    <tr class="<?= ($user->user_active == 0 ? 'inactive' : 'active'); ?>">
        <td><?= $user->user_id; ?></td>

        <td class="avatar">
            <?php if (isset($user->user_avatar_link)) { ?>
                <img src="<?= $user->user_avatar_link; ?>"/>
            <?php } ?>
        </td>

        <td><?= $user->user_name; ?></td>
        <td><?= $user->user_email; ?></td>
        <td><?= ($user->user_active == 0 ? 'No' : 'Yes'); ?></td>

        <td>
            <select name="user_account_type" form="<?= $formId; ?>">
                <?php foreach ($this->groups as $group) { ?>
                    <option value="<?= $group->group_id; ?>"
                        <?php if ($user->user_account_type == $group->group_id) echo 'selected'; ?>>
                        <?= htmlspecialchars($group->group_name); ?>
                    </option>
                <?php } ?>
            </select>
        </td>

        <td>
            <a href="<?= Config::get('URL') . 'profile/showProfile/' . $user->user_id; ?>">Profile</a>
        </td>

        <td>
            <input type="number" name="suspension" form="<?= $formId; ?>" />
        </td>

        <td>
            <input type="checkbox" name="softDelete" form="<?= $formId; ?>" <?php if ($user->user_deleted) { ?> checked <?php } ?> />
        </td>

        <td>
            <form id="<?= $formId; ?>" action="<?= Config::get("URL"); ?>admin/actionAccountSettings" method="post">
                <input type="hidden" name="user_id" value="<?= $user->user_id; ?>" />
                <input type="submit" value="Senden" />
            </form>
        </td>
    </tr>
<?php } ?>
            </table>
        </div>
    </div>
</div>
