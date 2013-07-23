<!DOCTYPE html>
<head>
<meta charset="UTF-8">

<link rel="stylesheet" type="text/css" href="/wp-content/themes/xss/style.css">
<style>
.header{
	padding-top: 50px;
}

.container{
	padding: 0 20px;
	width: 530px;
}

#add-comment, 
.comments .comment{
	position: relative;
	padding: 39px 15px 15px;
	background-color: #f5f5f5;

	border: 1px solid #DDD;

	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}

.comments .comment{
	padding-top: 30px;
	margin-bottom: 5px;
}

#add-comment:after,
.comments .user-name{
  position: absolute;
  top: -1px;
  left: -1px;
  padding: 5px 10px;
  font-size: 11px;
  font-weight: bold;
  background-color: #f5f5f5;
  border: 1px solid #ddd;
  color: #9da0a4;
  -webkit-border-radius: 4px 0 4px 0;
     -moz-border-radius: 4px 0 4px 0;
          border-radius: 4px 0 4px 0;
}

#add-comment:after {
	content: "说点什么";
	font-size: 15px;
}

#add-comment .submit{
	font-size: 22px;
	font-size: 1.57142858rem;
	float: right;
	position: absolute;
	right: 0;
	top: -52px;
	border-radius: 4px 0 4px 0;
}

#add-comment textarea{
	border-radius: 4px;
	border: 1px solid #ddd;
	width: 482px;
	height: 100px;
	margin-bottom: 5px;
	resize: none;
}

.form-wrap{
	padding-bottom: 10px;
	border-bottom: 1px solid #ddd;
}

.row{
	position: relative;
}

.comments{
	padding-top: 10px;
	border-top: 1px solid #f5f5f5;
}

</style>
<script>var __loaderConfig={appBase:'s/j/app/',libBase:'lib/1.0/',server:'i{n}.dpfile.com'};</script>
<script src="http://i1.dpfile.com/lib/1.0/neuron-active.min.90fe87a80caba2df40b5066d9f3360c2.js"></script>
<script src="http://i3.dpfile.com/x_x/version.min.v1304271057.js"></script>

</head>

<body>

<div class="container">

<div class="header"></div>

<div class="form-wrap">
	<form class="form" id="add-comment">
		<textarea id="J_area"></textarea>
		<div class="row input-row">
			<input type="submit" id="J_add" class="submit" value="提交" disabled />
		</div>
	</form>
</div>

<ul class="comments" id="J_comments"><?php

$date = date('Y-m-d H:i:s', 0);

$comments = $wpdb -> get_results( "SELECT user, comment, id FROM $table_name where time > '$date' ORDER BY id DESC LIMIT 100" );

foreach ($comments as $comment) {
	?><div class="comment">
	<span class="user-name"><?php echo $comment -> user ?></span>
	<div class="content"><?php echo $comment -> comment ?></div>
</div><?php

}


?></ul>

</div>

<script type="text/x-neuron-tpl" id="J_tpl-comment"><div class="comment">
	<span class="user-name">@{it.user}</span>
	<div class="content">@{it.comment}</div>
</div></script>

<script>
DP.data({
	now: <?php echo time(); ?>	
});

DP.ready(function() {

var REGEX_STRIP_TAGS = /<.*?>/g;
var POLL_INTERVAL = 3000;

DP.provide(['io/ajax', 'mvp/tpl'], function(DP, Ajax, Tpl) {
    
	var templateFn = Tpl.parse( $('#J_tpl-comment').html() );
	var timer;

	function poll(){
		read.send({
			after: DP.data('now')
		});

		clearTimeout(timer);
		timer = setTimeout(function(){
			poll(true);

		}, POLL_INTERVAL);
	};

	function send_message(comment){
		send.send({
			comment: comment
		});
	};

	// PAY ATTENSION!
	window.send_message = send_message;

	var read = new Ajax({
		url: '/read',
		method: 'GET',
		isSuccess: function(json) {
		    return json && json.code === 200;
		}

	}).on({
		success: function(json){
			DP.data('now', json.now);

			json.comments.forEach(function(comment) {
			    $.create('li').html( templateFn(comment) ).inject(comments_wrap, 'top');
			});
		}
	});

	var send = new Ajax({
		url: '/save',
		method: 'GET'

	}).on({
		success: function(json){
			if(json.code === 403){
				location.href = '/wp-login';
				
			}else if(json.code === 200){
                window.location.reload(true);

			}else{
				alert(json.msg);
			}
		}
	});

	// fetching comments
	// poll();

	// button events
	var area = $('#J_area');
	var comments_wrap = $('#J_comments');
	var button = $('#J_add').on('click', function(e) {
	    e.prevent();

	    var text = area.val().replace(REGEX_STRIP_TAGS, '');
	    // var text = area.val();
	    area.val('');

	    send_message(text);
	    poll(true);
	
	}).attr('disabled', false);


	// a hahahaha!
	// setTimeout(function(){ location.reload(); }, 30000);

});


});
</script>


</body>

</html>
<?php get_footer(); ?>