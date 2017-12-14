<?php
include 'boot.php';
if(!$_SESSION['login']){
	header('Location: login.php');
	exit;
}
if(!is_dir('pages'))
{
	if(!mkdir('pages'))
		die('Cannot create "pages" directory, make sure is writtable.');

	if(! touch('pages/index.json') ){
		die('Cannot write on "pages" directory, make sure is writtable.');
	}

}
$pages	= [];
foreach (scandir('pages') as $key => $value) {
	if($value == '.' || $value == '..')
		continue;
	$pages[]	= basename($value,'.json');
}

if(empty($pages)){
	if(! touch('pages/index.json') ){
		die('Cannot write on "pages" directory, make sure is writtable.');
	}
	$pages	= array('index');
}
if(isset($_GET['delete'])){
	$file	= 'pages/' . urlencode($_GET['delete']) . '.json';
	if(is_file($file) && !unlink($file))
	{
		die('Fail delete.');
	}
	header('Location: editor.php');
	exit;
}
if(@$_GET['new_page']){
	$new_page	= strtolower(strtr($_GET['new_page'], '	 *"+&#\'','________'));
	if(!is_file('pages/' . $new_page . '.json'))
	{
		if(! touch('pages/' . $new_page . '.json') ){
			die('Cannot write on "pages" directory, make sure is writtable.');
		}
	}
	header('Location: editor.php?page=' . $_GET['new_page']);
	exit;
}

if(! isset($_GET['page']) ){
	header('Location: editor.php?page=' . @$pages[0]);
	exit;
}

$current= $_GET['page'];
if(!in_array($current, $pages))
{
	die("Page not found");
}

$file	= 'pages/' . urlencode($current) . '.json';

if(!empty($_POST) ){
	$data	= $_POST;
	if(!file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT))){
		die("Make sure directory 'pages' is writable.\n Cannot save: $file.");
	}
}else{
	$data	= json_decode(file_get_contents($file), true);
}


$title	= "Edit Landing page";
?>
<!DOCTYPE html>
<html>
<head>
	<title><?= $title ?></title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css">
 	<link href="css/style.css" rel="stylesheet">
 	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</head>
<body>
<nav id="menu" class="container">
	<div class="nav-wrapper">
		<a href="/" class="brand-logo right"><img style="vertical-align: middle; max-height: 100%" src="files/logo-ajls-flat-white.png"></a>
		<ul class="">
			<?php foreach($pages as $key => $page):
				$cls	= '';
				if( ($current && $page == $current) || (!$page && $key === 0) ){
					$cls	= 'active';
				}
			?>
				<li class="<?= $cls ?>"><a href="editor.php?page=<?= $page ?>"><?= ucwords( strtr($page,'-_','  ') ) ?></a></li>
			<?php endforeach ?>
			<li><a href="#!" onclick="return newPage()">NEW PAGE</a></li>
			<li><a href="logout.php">Logout</a></li>
		</ul>
	</div>
</nav>
<div class="container">
	<form method="post">
		<div class="center">
			<h2><?= ucwords( strtr($current,'-_','  ') ) ?></h2>
			<a href="index.php?page=<?= $current ?>" target="_BLANK" class="btn">Preview</a>
			<?php if($current != 'index'): ?>
				<a href="editor.php?delete=<?= $current ?>" onclick="return confirm('Delete this page?')" class="btn red">Delete</a>
			<?php endif ?>
		</div>
		<div class="row">
			<div class="col s6">
				<div class="input-field">
					<input name="title" type="text" value="<?= @$data['title'] ?>">
					<label>Title</label>
				</div>
				<div class="input-field">
					<input name="description" type="text" value="<?= @$data['description'] ?>">
					<label>Description</label>
				</div>
				<div class="input-field">
					<input name="keywords" type="text" value="<?= @$data['keywords'] ?>">
					<label>Keywords</label>
				</div>
			</div>
			<div class="col s6">
				<div class="input-field">
					<input class="jscolor" name="page_background_color" type="text" value="<?= @$data['page_background_color'] ?>">
					<label>Page Background Color</label>
				</div>
				<div class="input-field">
					<input name="page_background_image" type="text" value="<?= @$data['page_background_image'] ?>">
					<label>Page Background Image URL</label>
				</div>
				<p>
					<input id="page_background_fit" <?= @$data['page_background_fit'] ? 'checked' : '' ?> type="checkbox" name="page_background_fit" value="1" />
					<label for="page_background_fit">Fit Background</label>
				</p>
			</div>
		</div>
		<h3 class="center">
			Content Area
		</h3>
		<div class="row">
			<div class="col s6">
				<div class="input-field">
					<select name="content_size">
					<?php
					$sizes	= array(
						's10 m6 l4 offset-m3 offset-l4 offset-s1' => 'Small',
						's10 m8 l6 offset-m2 offset-l3 offset-s1' => 'Medium',
						's12 m10 offset-m1' 		=> 'Large',
						's12' => 'Full Page',
						);
					foreach ($sizes as $key => $value)
					{
						$selected	= '';
						if($key == @$data['content_size'])
							$selected	= 'selected';
						printf('<option value="%s" %s>%s</option>', $key, $selected, $value);
					}
					?>
					</select>
					<label>Content Size</label>
				</div>
				<p>
					<input id="central" <?= @$data['content_central'] ? 'checked' : '' ?> type="checkbox" name="content_central" value="1" />
					<label for="central">Fixed Center</label>
				</p>
				<p>
					<input id="content_background_fit" <?= @$data['content_background_fit'] ? 'checked' : '' ?> type="checkbox" name="content_background_fit" value="1" />
					<label for="content_background_fit">Fit Background</label>
				</p>
			</div>
			<div class="col s6">
				<div class="input-field">
					<input name="content_background_image" type="text" value="<?= @$data['content_background_image'] ?>">
					<label>Background Image URL</label>
				</div>
				<div class="input-field">
					<input class="jscolor" name="content_background_color" type="text" value="<?= @$data['content_background_color'] ?>">
					<label>Background Color</label>
				</div>
				<div class="input-field">
					<input class="jscolor" name="content_color" type="text" value="<?= @$data['content_color'] ?>">
					<label>Text Color</label>
				</div>
			</div>
		</div>
		<div class="input-field">
		<textarea name="content"><?= @$data['content'] ?></textarea>
		</div>
		<div class="input-field">
			<button type="submit" class="btn green">Save</button>
		</div>
		<div class="fixed-action-btn">
			<button type="submit" class="btn-floating btn-large waves-effect waves-light green">Save</button>
		</div>
	</form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>
<script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
<script src="js/jscolor.min.js"></script>
<script>
	tinymce.init({
		selector:'textarea',  height: 500,
		menubar: false,
		plugins: [
			'advlist autolink lists link image charmap print preview anchor',
			'searchreplace visualblocks code fullscreen',
			'insertdatetime media table contextmenu paste code'
		],
		toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
		content_css: 'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css'
		});
	$(document).ready(function() {
    $('select').material_select();
  });
	function newPage(){
		var name	= prompt('Slug/File name');
		if(name){
			document.location = 'editor.php?new_page=' + name;
		}
	}
 </script>

</body>
</html>