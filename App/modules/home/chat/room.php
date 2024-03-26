<?php
if (!defined('_CODE')) {
    die('Access denied...');
}
$data = [
    'titlePage' => 'Chat room'
];

if (isGet()) {
    $filterAll = filter();
    $userId = $filterAll['userId'];
    $listFriend = getRaws("SELECT * FROM users");
}
if (!checkLogin()) {
    reDirect('?module=auth&page=login');
}
layouts('headerRoom', $data)
?>

<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />

<div class="container">
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card chat-app">
                <div id="plist" class="people-list">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Search...">
                    </div>
                    <ul class="list-unstyled chat-list mt-2 mb-0">

                        <?php
                        if (!empty($listFriend)) :
                            $count = 0;
                            foreach ($listFriend as $item) :
                                $friendId = $item['id'];
                                if($friendId == $userId) {
                                    continue;
                                }


                                $count++;
                        ?>
                                <li class="clearfix">
                                    <a  class="clearfix" href="?module=home&page=chat/chatWith&userId=<?php echo $userId ?>&friendId=<?php echo $friendId ?>">
                                        <img src="<?php echo $item['profileImage'] ? $item['profileImage'] :  "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?w=826&t=st=1710127291~exp=1710127891~hmac=10efc92f9bddd8afe06fa86d74c0caf109f33b79794fd0fc982a01c8bff70758"; ?>" class="mr-3 rounded-circle" width="50" alt="User" />
                                        <div class="about">
                                            <div class="name"><?php echo $item['fullname'] ?></div>
                                            <div class="status"> <i class="fa fa-circle offline"></i> left 7 mins ago </div>
                                        </div>
                                    </a>
                                </li>
                            <?php
                            endforeach;
                        else :
                            ?>
                            <tr>
                                <td>
                                    <div class="alert alert-danger text-center">None of Friend</div>
                                </td>
                            </tr>
                        <?php

                        endif;
                        ?>

                    </ul>
                </div>
                <div class="chat">
                    
                    <div class="chat-history" style="height: 569px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
layouts('footer')
?>