
var ajaxPath = "/socialmedia/_helpers/_ajax.php"; // path to file which handles AJAX requests

// if user wants to replace his post image with another one
function changeFileInputStatus() {
    if (document.getElementById("replaceimage").checked) {
        document.getElementById("image").required = true;
        document.getElementById("image").disabled = false;
    } else {
        document.getElementById("image").required = false;
        document.getElementById("image").disabled = true;
        document.getElementById("submitbutton").disabled = false;
    }
}

// when post gets liked we don't want to refresh the page everytime
// AJAX requests included
function likeFunction(pUser_Id, pPost_Id){
    // get number of likes of post
    var likes = (document.getElementById("like" + pPost_Id).textContent.match(/\d+\.\d+|\d+\b|\d+(?=\w)/g) || [] ).map(function (v) {return +v;}).shift();
    var likesText = document.getElementById("like" + pPost_Id).textContent.split(" ")[1];
    var text = document.getElementById("like" + pPost_Id).textContent.replace(likes + " " + likesText, "");
    var icon = document.getElementById("heart" + pPost_Id); // get heart font awesome icon

    if(icon.classList.contains("far") && !icon.classList.contains("fas")){
        // full heart (liked)
        icon.classList.remove("far");
        icon.classList.add("fas");
    }
    else{
        // empty heart (not liked)
        icon.classList.remove("fas");
        icon.classList.add("far");
    }

    // first AJAX call: check if user has already liked post or not
    $.ajax(
        {
            type: 'POST',
            url: ajaxPath,
            data:
            {
                'functiontocall': 'hasUserLikedPostAjax', // function to call in _ajax.php
                'user_id': pUser_Id,
                'post_id': pPost_Id
            },
            success:function(response)
            {
                // second AJAX call: like post (make changes in database)
                $.ajax(
                    {
                        type: 'POST',
                        url: ajaxPath,
                        data:
                        {
                            'functiontocall': 'likePostAjax',
                            'user_id': pUser_Id,
                            'post_id': pPost_Id
                        }
                    }
                );
                response = response.split('<returnValue>').pop().split('</returnValue>')[0]; // return value if user has liked post "true" or "false"
                if(response == "true"){
                    likes--;
                }
                else{
                    likes++;
                }
                likesText = likes == 1 ? "like" : "likes"; // if amount of likes is at 1 use singular
                document.getElementById("like" + pPost_Id).textContent = likes + " " + likesText + text; // set text below image (e.g. 34 likes . 17 comments)
            }
        }
    );
}

// when post gets commented we don't want to refresh the page everytime
function commentFunction(pUser_Id, pUsername, pPost_Id){
    var pText = document.getElementById("comment").value;
    var date = new Date();
    var weekdays = new Array(7); // define array to display current day of week with date.getDay()
        weekdays[0] = "Sunday";
        weekdays[1] = "Monday";
        weekdays[2] = "Tuesday";
        weekdays[3] = "Wednesday";
        weekdays[4] = "Thursday";
        weekdays[5] = "Friday";
        weekdays[6] = "Saturday";
    // information text for comment
    var info = "commented on " + weekdays[date.getDay()] + ", " + (date.getDate() < 10 ? '0' : '') + 
        date.getDate() + "." + (date.getMonth()+1 < 10 ? '0' : '') + (date.getMonth()+1) + "." + date.getFullYear() + " at " + 
        (date.getHours() < 10 ? '0' : '') + date.getHours() + ":" + (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();

    if(pText){
        // AJAX save comment in database
        $.ajax(
            {
                type: 'POST',
                url: ajaxPath,
                data:
                {
                    'functiontocall': 'commentPostAjax',
                    'user_id': pUser_Id,
                    'post_id': pPost_Id,
                    'text' : pText
                },
                success:function()
                {
                    document.getElementById("comment").value = "";

                    // AJAX call get comment id posted to post with javascript
                    $.ajax(
                        { 
                            type: 'POST',
                            url: ajaxPath,
                            data:
                            {
                                'functiontocall': 'getCommentIdAjax'
                            },
                            success:function(response)
                            {
                                var commentId = parseInt(response.split('<returnValue>').pop().split('</returnValue>')[0], 10);
                                // AJAX call get amount of comments
                                $.ajax(
                                    { 
                                        type: 'POST',
                                        url: ajaxPath,
                                        data:
                                        {
                                            'functiontocall': 'getCommentsByIdAjax',
                                            'post_id': pPost_Id
                                        },
                                        success:function(response)
                                        {
                                            var numberOfComments = parseInt(response.split('<returnValue>').pop().split('</returnValue>')[0], 10);
                                            if(numberOfComments - 1 >= 1){ // add comment to comment section if already commented
                                                document.getElementById("comments").innerHTML += 
                                                "<div class='flex' id='commentDiv" + commentId + "'>" + 
                                                    "<div class='p'>" + 
                                                        "<b>" + pUsername + "</b> " + pText + 
                                                        "<div class='createdInfo'>" + info + "</div>" + 
                                                    "</div>" + 
                                                    "<a onclick='confirmDelete(" + commentId + ", \"comment\", " + pPost_Id + ")' class='deleteComment'><i class='fas fa-trash-alt'></i></a>" + 
                                                "</div>";
                                            }
                                            else{ // clear information that no one has commented if no one has commented yet
                                                document.getElementById("comments").innerHTML = 
                                                "<div class='flex' id='commentDiv" + commentId + "'>" + 
                                                    "<div class='p'>" + 
                                                        "<b>" + pUsername + "</b> " + pText + 
                                                        "<div class='createdInfo'>" + info + "</div>" + 
                                                    "</div>" + 
                                                    "<a onclick='confirmDelete(" + commentId + ", \"comment\", " + pPost_Id + ")' class='deleteComment'><i class='fas fa-trash-alt'></i></a>" + 
                                                "</div>";
                                            }
                                        }
                                    }
                                );
                            }
                        }
                    );
                }
            }
        );
    }
}

// user has to submit decision if he wants to delete...
// - comment
// - post
// (- his user account (not embedded here))
function confirmDelete(p_Id, pType, pPost_Id){
    if(confirm("Are you sure you want to delete this " + pType + "?")){
        switch(pType) {
            case "post":
                window.location.href = "/socialmedia/_helpers/_delete.php?postid=" + p_Id;
                break;
            case "comment":
                // AJAX call delete comment
                $.ajax(
                    { 
                        type: 'POST',
                        url: ajaxPath,
                        data:
                        {
                            'functiontocall': 'deleteCommentAjax',
                            'comment_id': p_Id
                        },
                        success:function()
                        {
                            var comment = document.getElementById("commentDiv" + p_Id);
                            document.getElementById("comments").removeChild(comment);
                            // AJAX call check if amount of comments is 0 to display no one has commented message
                            $.ajax(
                                { 
                                    type: 'POST',
                                    url: ajaxPath,
                                    data:
                                    {
                                        'functiontocall': 'getCommentsByIdAjax',
                                        'post_id': pPost_Id
                                    },
                                    success:function(response)
                                    {
                                        // get number of comments (convert to int)
                                        var numberOfComments = parseInt(response.split('<returnValue>').pop().split('</returnValue>')[0], 10);
                                        if(numberOfComments == 0){
                                            document.getElementById("comments").innerHTML = "<p><i>No one has commented yet. Be the first!</i></p>";
                                        }
                                    }
                                }
                            );
                        }
                    }
                );
                break;
            default:
                console.log("This isn't a valid type!");
                break;
        }
    }
}