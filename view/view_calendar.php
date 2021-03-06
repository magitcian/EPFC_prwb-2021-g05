<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset="UTF-8">
    <title>Calendar</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link href='lib/FullCalendar/main.css' rel='stylesheet' />
    <link href="css/calendar.css" rel="stylesheet" type="text/css"/>
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
    <link href="css/menu.css" rel="stylesheet" type="text/css"/>
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.min.js" type="text/javascript"></script>
    <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
    <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.theme.min.css" rel="stylesheet" type="text/css"/>
    <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.structure.min.css" rel="stylesheet" type="text/css"/>
    <script src="lib/jquery-validation-1.19.3/jquery.validate.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/popper.js/dist/umd/popper.min.js" type="text/javascript"></script>
    <script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js" type="text/javascript"></script>
    <script src='lib/FullCalendar/main.js'></script>
    <script>
      let boards = <?= $boards ?>;
      let events = new Array();
      let calendar;
      let boardCheck;
      let boards_js;

      $(function(){
        boardCheck = $('#boardCheck');

        displayCheckboxBoard();
        updateBoards();

        let calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',

          eventDidMount: function(info) {
                      $(info.el).tooltip({ 
              title: info.event.extendedProps.description,
              placement: "top",
              trigger: "hover",
              container: "body"
            });
          },

          eventClick: function(event) {
            changeModalInfo(event.event._def);
            $('#details_modal').dialog({
                    resizable: false,
                    height: 300,
                    width: 600,
                    modal: true,
                    autoOpen: true,
                    buttons: {
                        Close: function () {
                            $(this).dialog("close");
                        }
                    }
                });
          },

          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
          },

          eventSources: [
            {
              url: 'calendar/get_events_service',
              method: 'POST', 
              extraParams: function(){
                return {
                  boards: JSON.stringify(boards_js)
                }
              },
              failure: function() {
                alert('there was an error while fetching events!');
              }
            }
          ]


        });
        calendar.render();
      });

      function changeModalInfo(event) {
        // console.log('event:', event);
        let modal = document.getElementById('details_modal');

        let title = event.title;
        let description = event.extendedProps.description;

        modal.innerHTML = "<h2>" + title + "</h2>"
                        + "</br>"
                        + "<p>" + description + "</p>";
      }

      function refreshCalendar(){
        updateBoards();
        calendar.refetchEvents();
      }

      function updateBoards() {
        console.log('update boards');
        boards_js = getCheckedBoards();
        console.log(boards_js);
      }

      function getCheckedBoards() {
        let boards_html = document.getElementsByClassName('board');
        let checkedBoards = [];
        for(let board_html of boards_html)
          if(board_html.checked)
            checkedBoards.push(
              {
                id: board_html.getAttribute('board_id'), 
                color: board_html.getAttribute('color')
              });
        return checkedBoards;
      }

      function displayCheckboxBoard(){
        let htmlMyBoard = "";
        let htmlOtherBoard = "";
        let htmlNotSharedBoard = "";
          for (let b of boards) {
              if(b.type === "my_boards"){
                htmlMyBoard += "<input class='board' color='" + b.color + "' type='checkbox' board_id='" + b.id + "' onclick='refreshCalendar()' checked>";
                htmlMyBoard += "<label style='color: " + b.color + ";'>&ensp;" + b.title + "&ensp;&ensp;</label> ";
              }else if(b.type === "other_boards"){
                htmlOtherBoard += "<input class='board' color='" + b.color + "' type='checkbox' board_id='" + b.id + "' onclick='refreshCalendar()' checked>";
                htmlOtherBoard += "<label style='color: " + b.color + ";'>&ensp;" + b.title + "&ensp;&ensp;</label> ";
              }else if(b.type === "not_shared_boards"){
                htmlNotSharedBoard += "<input class='board' color='" + b.color + "' type='checkbox' board_id='" + b.id + "' onclick='refreshCalendar()' checked>";
                htmlNotSharedBoard += "<label style='color: " + b.color + ";'>&ensp;" + b.title + "&ensp;&ensp;</label> ";
              }
          }
          if(htmlMyBoard !== ""){
            htmlMyBoard = "<h3>Your boards</h3>" + htmlMyBoard;
          }
          if(htmlOtherBoard !== ""){
            htmlOtherBoard = "<h3>Other boards</h3>" + htmlOtherBoard;
          }
          if(htmlNotSharedBoard !== ""){
            htmlNotSharedBoard = "<h3>Not shared boards</h3>" + htmlNotSharedBoard;
          }
          boardCheck.html(htmlMyBoard + htmlOtherBoard + htmlNotSharedBoard);
      }

    </script>
  </head>
  <body>
    <?php
          $menu_title = "";
          $menu_subtitle = "Boards";
          include("menu.php");
    ?>
    <div class="content">
      <div id='boardCheck'></div>
      <br />
      <div id='calendar'></div>
      <noscript>
        Your browser does not support JavaScript!
      </noscript>
    </div>
    <div id="details_modal" hidden>
        <h2>Title</h2>
        <p></p>
        <p>Description</p>
    </div>
  </body>
</html>