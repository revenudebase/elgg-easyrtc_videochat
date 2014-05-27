<?php
/**
 * elgg-nodejs plugin
 * Provide tools for interaction with node.js (socket.io) and elgg
 *
 * @package elgg-nodejs
 *
 * Chat view
 */

$site_url = elgg_get_site_url();
?>

<ul id="chat-container">
	<li id="online-users" class="chat-box hidden mrm">
		<div class="elgg-head link">
			<h4 class="float"></h4>
			<ul class="elgg-menu elgg-menu-hz float-alt hidden">
				<li class="elgg-menu-item-chat-settings">
					<a href="#" rel="toggle" class="fi-widget"></a>
				</li>
				<li class="elgg-menu-item-toggle">
					<a href="#" rel="toggle" class="fi-minus"></a>
				</li>
			</ul>
		</div>
		<div class="elgg-body hidden">
			<div id="search-online-users" class="chat-box-top">
				<input placeholder="<?php echo elgg_echo('videochat:search_online_users'); ?>">
				<span class="fi-magnifying-glass"></span>
			</div>
			<ul>
			</ul>
		</div>
	</li>
</ul>

<script id="online-users-template" type="text/template">
	{{#each users}}
	<li id="elgg-user-{{this.guid}}" class="elgg-item elgg-item-user link" data-guid="{{this.guid}}" data-name="{{this.name}}">
		<div class="elgg-image-block clearfix">
			<div class="elgg-image">
				<div class="elgg-avatar elgg-avatar-tiny">
					<img src="http://127.0.0.1/mfrb/elgg/_graphics/icons/user/defaulttiny.gif" alt="{{this.name}}" title="{{this.name}}">
				</div>
			</div>
			<div class="elgg-body">
				<h4>{{this.name}}</h4>
				<div class="elgg-subtext"></div>
			</div>
		</div>
	</li>
	{{/each}}
</script>

<script id="chatroom-template" type="text/template">
<li id="chatroom-{{channel}}" class="chat-box chat-room mrm elgg-state-active">
		<div class="elgg-head link">
			<h4 class="float">{{data.chat_name}}</h4>
			<ul class="elgg-menu elgg-menu-hz float-alt hidden">
				<li class="elgg-menu-item-chat-settings">
					<a href="#" rel="toggle" class="fi-widget"></a>
				</li>
				<li class="elgg-menu-item-toggle">
					<a href="#" rel="toggle" class="fi-minus"></a>
				</li>
				<li class="elgg-menu-item-close">
					<a href="#" rel="toggle" class="fi-x"></a>
				</li>
			</ul>
		</div>
		<div class="elgg-body hidden">
			<div class="chat-box-top hidden">
				<input placeholder="<?php echo elgg_echo('videochat:add_user'); ?>">
				<span class="fi-magnifying-glass"></span>
			</div>
			<ul class="chat-body">
			</ul>
			<div class="chat-box-bottom">
				<textarea placeholder="<?php echo elgg_echo('videochat:add_message'); ?>"></textarea>
			</div>
		</div>
	</li>
</script>

<script id="chat-message-template" type="text/template">
	<li id="elgg-user-{{sender.guid}}" class="elgg-item elgg-item-user pas" data-guid="{{sender.guid}}" data-name="{{sender.name}}">
		<div class="elgg-image-block clearfix pan">
			<div class="elgg-image">
				<div class="elgg-avatar elgg-avatar-tiny">
					<img src="http://127.0.0.1/mfrb/elgg/_graphics/icons/user/defaulttiny.gif" alt="{{sender.name}}" title="{{sender.name}}">
				</div>
			</div>
			<div class="elgg-body">
				<h4>{{sender.name}}</h4>
				<p time="{{time}}">{{msg}}</p>
			</div>
		</div>
	</li>
</script>

