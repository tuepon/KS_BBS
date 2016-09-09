<?php
/**
 * reply.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/06
 */

namespace KSBBS;

require 'common.php';

$res = BBS::reply();
?>
<!DOCTYPE HTML>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title><?= BBS_REPLY; ?></title>
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
				<h1 class="blog-title"><?= BBS_REPLY; ?></h1>
			</div>

			<?php if (isset($res['errors']['csrf_token']) && !$res['errors']['csrf_token']) : ?>
				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-danger alert-dismissible">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h4><i class="icon fa fa-ban"></i> Alert!</h4>
							<?= BBS_ERR_CSRF; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (isset($res['success']) && $res['success']) : ?>

				<div class="row">
					<div class="col-sm-12">
						<div class="alert alert-success">
							<h4><i class="icon fa fa-check"></i> Success!</h4>
							<p><?= BBS_POST_SUCCESS; ?></p>
							<p><a href="index.php"><?= BBS_TO_TOP; ?></a></p>
						</div>
					</div>
				</div>

			<?php else: ?>

				<div class="row">

					<div class="col-sm-12">

						<form action="" method="post" enctype="multipart/form-data">

							<div class="form-group<?php if (isset($res['errors']['username'])) : ?> has-error<?php endif; ?>">
								<label class="control-label" for="username">
									<?= BBS_POST_NAME; ?>
								</label>
								<input type="text" name="username" class="form-control" id="username" value="<?= h(filter_input(INPUT_POST, 'username')); ?>">
								<?php if (isset($res['errors']['username'])) : ?>
									<span class="help-block"><?= h($res['errors']['username']); ?></span>
								<?php endif; ?>
							</div>

							<div class="form-group<?php if (isset($res['errors']['comment'])) : ?> has-error<?php endif; ?>">
								<label class="control-label" for="comment">
									<?= BBS_POST_CONTENT; ?>
								</label>
								<textarea name="comment" class="form-control" id="comment" rows="6"><?= h(filter_input(INPUT_POST, 'comment')); ?></textarea>
								<?php if (isset($res['errors']['comment'])) : ?>
									<span class="help-block"><?= h($res['errors']['comment']); ?></span>
								<?php endif; ?>
							</div>

							<?php if (BBS_FUNC_IMAGE) : ?>

								<div class="form-group">
									<label class="control-label" for="images">
										<?= BBS_POST_IMAGE; ?>
									</label>

									<div class="row">
										<div class="col-sm-6"><input type="file" name="images[]" id="images"></div>
										<?php if (isset($res['errors']['images'][0])) : ?>
											<div class="col-sm-6 has-error"><span class="help-block"><?= h($res['errors']['images'][0]); ?></span></div>
										<?php endif; ?>
									</div>

									<div class="row">
										<div class="col-sm-6"><input type="file" name="images[]"></div>
										<?php if (isset($res['errors']['images'][1])) : ?>
											<div class="col-sm-6 has-error"><span class="help-block"><?= h($res['errors']['images'][1]); ?></span></div>
										<?php endif; ?>
									</div>

									<div class="row">
										<div class="col-sm-6"><input type="file" name="images[]"></div>
										<?php if (isset($res['errors']['images'][2])) : ?>
											<div class="col-sm-6 has-error"><span class="help-block"><?= h($res['errors']['images'][2]); ?></span></div>
										<?php endif; ?>
									</div>
								</div>

							<?php endif; ?>

							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<button class="btn btn-primary"><?= BBS_POST; ?></button>
										<a href="index.php" class="btn btn-default"><?= BBS_TO_TOP; ?></a>
										<input type="hidden" name="crsf_token" value="<?= h(CSRF::get()); ?>" />
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group<?php if (isset($res['errors']['delete_key'])) : ?> has-error<?php endif; ?>">
										<div class="input-group">
											<span class="input-group-addon"><?= BBS_POST_DELKEY; ?></span>
											<input type="text" name="delete_key" class="form-control" id="delete_key" value="<?= h(filter_input(INPUT_POST, 'delete_key')); ?>">
										</div>
										<?php if (isset($res['errors']['delete_key'])) : ?>
											<span class="help-block"><?= h($res['errors']['delete_key']); ?></span>
										<?php endif; ?>
									</div>
								</div>
							</div>

						</form>

					</div>
				</div>
			<?php endif; ?>
		</div>
		<script type="text/javascript" src="//code.jquery.com/jquery-3.1.0.min.js"></script>
		<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>