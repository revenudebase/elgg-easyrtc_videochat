#chat-container {
	position: fixed;
	bottom: 0;
	right: 0;
}
.chat-box {
	width: 260px;
	display: inline-block;
	vertical-align: bottom;
	box-shadow: 0px 0px 4px rgba(100, 100, 100, 0.5);
}
.chat-box.hidden {
	display: none;
}
.chat-box > .elgg-head {
	background: #444;
	min-height: 30px;
	border-radius: 2px 2px 0 0;
	padding: 5px 7px 5px 10px;
}
.chat-box > .elgg-head h4 {
	color: white;
}
.chat-box .elgg-avatar-tiny img {
	width: 25px;
}
.chat-box > .elgg-head a {
	color: white;
	padding: 1px 3px 2px;
}
.chat-box > .elgg-head a:hover {
	text-decoration: none;
}
.chat-box > .elgg-head .elgg-menu li {
	margin-top: -1px;
	width: 17px;
	text-align: center;
}
.chat-box > .elgg-head .elgg-menu a:hover {
	border-radius: 2px;
	background: #F1C40F;
	color: #444;
}
.chat-box > .elgg-body {
	border-right: 1px solid #ccc;
	border-left: 1px solid #ccc;
	background: white;
}
.chat-box.elgg-state-active > .elgg-head {
	cursor: default;
}
.chat-box.elgg-state-active > .elgg-head .fi-minus:before {
	margin-bottom: -3px;
	padding-top: 3px;
}
.chat-box.elgg-state-active > .elgg-head .elgg-menu {
	display: block;
}
.chat-box.elgg-state-active > .elgg-body {
	display: block;
}
.chat-box-top {
	position: relative;
	border-bottom: 1px solid #ccc;
}
.chat-box-top input {
	border: none;
	padding: 3px 20px 3px 5px;
}
.chat-box-top span {
	position: absolute;
	right: 5px;
	top: 7px;
	font-size: 1.2em;
	color: #CCC;
}
.chat-body {
	min-height: 100px;
	max-height: 300px;
	overflow-y: auto;
	padding-bottom: 15px;
}
.chat-body p {
	margin-bottom: 4px;
	white-space: pre-line;
	line-height: 1.1em;
	position: relative;
}
.chat-body p:before {
	content: attr(time);
	color: white;
	font-size: 75%;
	line-height: inherit;
	font-style: italic;
	position: absolute;
	opacity: 0;
	background: rgba(0, 0, 0, 0.5);
	top: -18px;
	padding: 2px 4px;
	right: 0px;
}
.chat-body p:hover:before {
	opacity: 1;
}
.chat-box-bottom {
	position: relative;
	border-top: 1px solid #ccc;
}
.chat-box-bottom textarea {
	border: none;
	padding: 2px 5px 1px;
	background: transparent;
	box-shadow: none;
	resize: none;
}


#online-users {
	width: 230px;
}
#online-users .elgg-body > ul li {
	border-bottom: 1px solid #EEE;
	padding: 0 8px;
}
#online-users .elgg-body > ul li:last-child {
	border: none;
}
#online-users .elgg-body > ul li:hover {
	background: #EEE;
}
#online-users .elgg-body h4 {
	color: #5097CF;
}