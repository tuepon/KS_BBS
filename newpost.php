<?php
/**
 * newpost.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/06
 */

namespace KSBBS;

require 'common.php';

$res = BBS::newThread();
?>
<!DOCTYPE HTML>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title>新規スレッド投稿</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/common.min.css" />

		<!--[if lt IE 9]>
		  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>

		<div class="blog-masthead">
			<div class="container">
				<nav class="blog-nav">
					<a class="blog-nav-item" href="#">Home</a>
					<a class="blog-nav-item active" href="#">BBS</a>
				</nav>
			</div>
		</div>

		<div class="container">

			<div class="blog-header">
				<h1 class="blog-title">新規スレッド投稿</h1>
			</div>

			<?php if (isset($res['errors']['csrf_token']) && !$res['errors']['csrf_token']) : ?>
				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-danger alert-dismissible">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4><i class="icon fa fa-ban"></i> Alert!</h4>
							二重投稿を検知したので、処理を中断しました。
						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="row">
				<div class="col-sm-12">

					<form action="" method="post">
						<div class="form-group<?php if (isset($res['errors']['title'])) : ?> has-error<?php endif; ?>">
							<label class="control-label" for="title">
								タイトル
							</label>
							<input type="text" name="title" class="form-control" id="title" value="<?= h(filter_input(INPUT_POST, 'title')); ?>">
							<?php if (isset($res['errors']['title'])) : ?>
								<span class="help-block"><?= h($res['errors']['title']); ?></span>
							<?php endif; ?>
						</div>
						<div class="form-group<?php if (isset($res['errors']['username'])) : ?> has-error<?php endif; ?>">
							<label class="control-label" for="username">
								お名前
							</label>
							<input type="text" name="username" class="form-control" id="username" value="<?= h(filter_input(INPUT_POST, 'username')); ?>">
							<?php if (isset($res['errors']['username'])) : ?>
								<span class="help-block"><?= h($res['errors']['username']); ?></span>
							<?php endif; ?>
						</div>
						<div class="form-group<?php if (isset($res['errors']['comment'])) : ?> has-error<?php endif; ?>">
							<label class="control-label" for="comment">
								内容
							</label>
							<textarea name="comment" class="form-control" id="comment" rows="6"><?= h(filter_input(INPUT_POST, 'comment')); ?></textarea>
							<?php if (isset($res['errors']['comment'])) : ?>
								<span class="help-block"><?= h($res['errors']['comment']); ?></span>
							<?php endif; ?>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<button class="btn btn-primary">投稿</button>
									<a href="index.php" class="btn btn-default">掲示板トップへ</a>
									<input type="hidden" name="crsf_token" value="<?= h(CSRF::get()); ?>" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group<?php if (isset($res['errors']['delkey'])) : ?> has-error<?php endif; ?>">
									<div class="input-group">
										<span class="input-group-addon">削除キー</span>
										<input type="text" name="delkey" class="form-control" id="delkey" value="<?= h(filter_input(INPUT_POST, 'delkey')); ?>">
									</div>
									<?php if (isset($res['errors']['delkey'])) : ?>
										<span class="help-block"><?= h($res['errors']['delkey']); ?></span>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</form>

				</div>
			</div>
		</div>
		<script type="text/javascript" src="//code.jquery.com/jquery-3.0.0.min.js"></script>
		<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>