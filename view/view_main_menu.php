<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Boards</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <link href="css/menu.css" rel="stylesheet" type="text/css"/>
        <link href="css/main.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
        <script src="lib/jquery-validation-1.19.3/jquery.validate.min.js" type="text/javascript"></script>
        <script src="lib/MyLib.js" type="text/javascript"></script>
        <script>
            $(function(){
                $('#form-add-board').validate({
                    rules: {
                        new_board_name: {
                            minlength: 3,
                            remote: {
                                url: 'board/available_board_title_service',
                                type: 'post',
                                data:  {
                                    board_title: function() { 
                                        return $("#input-board-name").val();
                                    }
                                }
                            }
                        }
                    },
                    messages: {
                        new_board_name: {
                            minlength: 'minimum 3 characters',
                            remote: 'this board title already exists'
                        }
                    }
                });
            });
        </script>
    </head>
    <body>
        <?php
            $menu_title = "Boards";
            $menu_subtitle = "";
            include("menu.php");
        ?>
        <div class="content">
            <h2>Your boards</h2>

            <div class="boards">
                <!-- your boards -->
                <?php foreach($personal_boards as $board): ?>
                    <a href=<?= "board/board/".$board->get_board_id() ?> class="btn btboardsMe">
                        <span class="board-title"><?= $board->get_title()?> (<?= $board->get_nb_columns()?> columns)</span>
                    </a>
                <?php endforeach; ?> 
                    
                <!-- add board form -->
                <form id="form-add-board" action="board/add" method="post" class="input-group form-my-boards">
                    <input id="input-board-name" name="new_board_name" type="text" placeholder="Add a board" class="form-control" value="<?= $new_board_name?>">
                    <button class="input-group-text btt-add-board" type="submit" name="bt_board_name"> 
                        <i class="fa fa-plus"></i>
                    </button>
                </form>
            </div>
            <?php if (count($errors) != 0): ?>
                <div class='errors'>
                    <p>Please correct the following error(s) :</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <h2>Boards shared with you</h2>
            <div class="boards">          
                <?php foreach($other_shared_boards as $board): ?>
                    <a href=<?= "board/board/".$board->get_board_id() ?> class="btn btboardsShared">
                        <span class="board-title"><?= $board->get_title()?> (<?= $board->get_nb_columns()?> columns)</span>
                        <br/>
                        <span class="author-name">by <?=$board->get_author_name()?></span>
                    </a>
                <?php endforeach; ?> 
            </div>
            <?php if ($user->get_role() === "admin"): ?>    
                <h2>Others' boards</h2>
                <div class="boards">          
                    <?php foreach($other_not_shared_boards as $board): ?>
                        <a href=<?= "board/board/".$board->get_board_id() ?> class="btn btboardsOther">
                            <span class="board-title"><?= $board->get_title()?> (<?= $board->get_nb_columns()?> columns)</span>
                            <br/>
                            <span class="author-name">by <?=$board->get_author_name()?></span>
                        </a>
                    <?php endforeach; ?> 
                </div> 
            <?php endif; ?> 
        </div>
    </body>
</html>