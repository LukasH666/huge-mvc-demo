<div class="container">
    <h1>Profiles</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>Benutzerübersicht</h3>
        <div>
            Diese Seite zeigt alle Benutzer im System inklusive Rolle und erweiterten Profilinformationen.
        </div>

        <br>

        <div>
            <table class="overview-table">
                <thead>
                <tr>
                    <td>Id</td>
                    <td>Avatar</td>
                    <td>Username</td>
                    <td>User's email</td>
                    <td>Activated ?</td>
                    <td>Role</td>
                    <td>Wohnort</td>
                    <td>Hobby</td>
                    <td>Bio</td>
                    <td>Link to user's profile</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($this->users as $user) { ?>
                    <tr class="<?= ($user->user_active == 0 ? 'inactive' : 'active'); ?>">
                        <td><?= htmlspecialchars($user->user_id); ?></td>
                        <td class="avatar">
    <?php if (!empty($user->profile_picture_filename)) { ?>
        <img class="profile-overview-picture"
             src="<?= Config::get('URL') . 'profile/picture/' . $user->user_id; ?>"
             alt="Profilbild">
    <?php } else { ?>
        -
    <?php } ?>
</td>
                        <td><?= htmlspecialchars($user->user_name); ?></td>
                        <td><?= htmlspecialchars($user->user_email); ?></td>
                        <td><?= ($user->user_active == 0 ? 'No' : 'Yes'); ?></td>
                        <td><?= htmlspecialchars($user->group_name); ?></td>
                        <td><?= htmlspecialchars($user->location ?? '-'); ?></td>
                        <td><?= htmlspecialchars($user->hobby ?? '-'); ?></td>
                        <td><?= htmlspecialchars($user->bio ?? '-'); ?></td>
                        <td>
                            <a href="<?= Config::get('URL') . 'profile/showProfile/' . $user->user_id; ?>">Profile</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    <script>
    $(document).ready(function () {
        $('.overview-table').DataTable();
    });
    </script>
</div>