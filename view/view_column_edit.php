<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit a column</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <link href="css/menu.css" rel="stylesheet" type="text/css"/>
        <link href="css/edit.css" rel="stylesheet" type="text/css"/>
        <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
        <script src="lib/jquery-validation-1.19.3/jquery.validate.min.js" type="text/javascript"></script>
        <script src="lib/MyLib.js" type="text/javascript"></script>
        <script>
            $(function(){
                $('#column-edit').validate({
                    rules: {
                        column_title: {
                            minlength: 3,
                            remote: {
                                url: 'column/available_column_title_service',
                                type: 'post',
                                data:  {
                                    column_id: <?= $column->get_column_id() ?>,
                                    column_title: function() { return $("#column-title").val();},
                                    board_id: <?= $column->get_board_id() ?>
                                }
                            }
                        }
                    },
                    messages: {
                        column_title: {
                            minlength: 'minimum 3 characters',
                            remote: 'this column title already exists in your board'
                        }
                    }
                });
            });

        </script>
    </head>
    <body>
        <?php
            $menu_title = $column->get_menu_title();
            $menu_subtitle = "Boards";
            include("menu.php");
        ?>
        <div class="content">
            <div class="header">
                <h2>
                    Edit a column
                </h2>
                Created <?= $column->get_duration_since_creation() ?> ago. <?= $column->get_last_modification()?"Modified ".$column->get_duration_since_last_edit()." ago.":"Never modified." ?>
            </div>
            <form action=<?= "column/save/" ?> method="post" id="column-edit" class="form">
                <input type="hidden" class="form-control" value="<?= $column->get_column_id() ?>" name="column_id">
                <div class="form-group title-group">
                    <label class="label" for="column-title">Title</label>
                    <input type="text" name="column_title" id="column-title" value="<?= isset($proposed_title) ? $proposed_title : $column->get_title() ?>" class="form-control">
                </div>
                <div class="form-group">
                    <label class="label" for="board-title">Board</label>
                    <input type="text" name="board-title" id="board-title" value="<?= $column->get_board_title() ?>" class="form-control" readonly>
                </div>
                <div class="buttons">
                    <a href=<?= "board/board/".$column->get_board_id()?> class="btn btn-primary cancel">Cancel</a>
                    <input type="submit" value="Edit this column" class="btn btn-primary submit">   
                </div>         
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
    </body>
</html>