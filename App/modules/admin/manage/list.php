<?php

if (!defined('_CODE')) {
    die('Access denied...');
}

$data = [
    'titlePage' => 'User management'
];
// if(checkAdmin()) {
//     reDirect('?module=users&action=list');
// }
$isAdmin = checkAdmin();
if(!$isAdmin) {
    reDirect('?module=home&page=forum/forum');
}
$smg = getFlashData('smg');
$smgType = getFlashData(('smg_type'));
// quey to users table




$listUsers = getRaws("SELECT * FROM users ORDER BY updateAt DESC");
// echo '<pre>';
// print_r($listUsers);
// echo '</pre>';

layouts('headerIn', $data);
?><div class="container">
    <hr>
    <h2>Manage users</h2>
    <?php
    if (!empty($smg)) {
            getSmg($smg, $smgType);
        }
    ?>
    <p>

        <a href="?module=admin&page=manage/add" >
            <button class="mg-btn primary">
                Add user <i class="fa-solid fa-plus"></i>

            </button>
            </a>
    </p>
    <table class="table table-bordered">
        <thead>
            <th>Number</th>
            <th>Full name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th>role</th>
            <th width="5%">Edit</th>
            <th width="5%">Delete</th>
        </thead>
        <tbody>
            <?php
            if (!empty($listUsers)):
                $count = 0;
                foreach($listUsers as $item):
                    $count ++;
            ?>
            <tr>

                <td><?php echo $count; ?></td>
                <td><?php echo $item['fullname']; ?></td>
                <td><?php echo $item['email']; ?></td>
                <td><?php echo $item['phone']; ?></td>
                <td><?php echo $item['status'] == 1 ? '<button class="btn btn-success btn-sm">Active</button>' :  '<button class="btn btn-danger btn-sm">Not active</button>'; ?></td>
                <td><?php echo $item['role']; ?></td>
                            
                <td><a href="<?php echo _WEB_HOST;?>/?module=admin&page=manage/edit&id=<?php echo $item['id']?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i></a></td>
                <td><a  style="<?php echo checkAdminInList($item['id']) ? 'display: none' : null ?>" href="<?php echo _WEB_HOST;?>/?module=admin&page=manage/delete&id=<?php echo $item['id']?>" onclick="return confirm('Delete this row?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a></td>
            </tr>

            <?php
                endforeach;
            else: 
                ?>
                <tr>
                    <td colspan="7">
                        <div class="alert alert-danger text-center">None of user</div>
                    </td>
                </tr>
                <?php

            endif;                
             ?>
        </tbody>
    </table>
</div>



<?php
layouts('footer')
?>