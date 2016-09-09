<?php
/**
 * index.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/06
 */

namespace KSBBS;

require 'common.php';

// スレッドを取得
$res = BBS::get(filter_input(INPUT_GET, 'order'));
?>
<!DOCTYPE HTML>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title><?= BBS_TITLE; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/lightbox.css">
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
				<h1 class="blog-title"><?= BBS_TITLE; ?></h1>
				<p class="lead blog-description"><?= BBS_DESCRIPTION; ?></p>
			</div>

			<div class="order">
				<span>ソート</span>
				<a href="?order=new"><?= BBS_ORDER_NEW; ?></a>
				<a href="?order=comment"><?= BBS_ORDER_COMMENT; ?></a>
			</div>

			<div class="row">

				<div class="col-sm-8 blog-main">

					<?php if (isset($res['rows']) && count($res['rows']) > 0) : ?>

						<div class="threads">

							<?php foreach ($res['rows'] as $r) : ?>

								<div class="thread" data-id="<?= h($r['id']) ?>">

									<h2><?= h($r['title']) ?></h2>

									<div class="username">
										Post by <?= h($r['username']) ?> at <?= h($r['create_at']) ?>
									</div>

									<div class="comment">
										<?= nl2br(h($r['comment'])) ?>
									</div>

									<div class="images row">
										<?php foreach (BBS::getImages($r['id']) as $img) : ?>
											<div class="col-xs-4">
												<a rel="lightbox[<?= h($r['id']); ?>]" href="<?= h('upfiles/' . $img['filename']); ?>">
													<img src="<?= h('upfiles/thumb/' . $img['filename']); ?>" width="100%" />
												</a>
											</div>
										<?php endforeach; ?>
									</div>

									<div class="comment-footer">
										<a href="reply.php?id=<?= h($r['id']) ?>">[ <?= BBS_REPLYING; ?> ]</a>
										<span><?= BBS_REPLY_COUNT; ?>: <?= h($r['comment_count']) ?></span>
									</div>

									<?php if (isset($r['reply']['count']) && $r['reply']['count'] > 0) : ?>

										<?php foreach ($r['reply']['rows'] as $i => $reply) : ?>

											<div class="reply">
												<h3>#<?= ($i + 1); ?> Re: <?= h($r['title']) ?></h3>

												<div class="username">
													Post by <?= h($reply['username']) ?> at <?= h($reply['create_at']) ?>
												</div>

												<div class="comment">
													<?= nl2br(h($reply['comment'])) ?>
												</div>

												<div class="images row">
													<?php foreach (BBS::getImages($reply['id']) as $img) : ?>
														<div class="col-xs-4">
															<a rel="lightbox[ch-<?= h($reply['id']); ?>]" href="<?= h('upfiles/' . $img['filename']); ?>">
																<img src="<?= h('upfiles/thumb/' . $img['filename']); ?>" width="100%" />
															</a>
														</div>
													<?php endforeach; ?>
												</div>
											</div>

										<?php endforeach; ?>

									<?php endif; ?>

								</div>

							<?php endforeach; ?>

						</div>

						<div class="row pagination_nav">
							<div class="col-xs-12">
								<?php echo pagination($res['count']); ?>
							</div>

						</div>

					<?php endif; ?>

				</div>
				<div class="col-sm-4">

					<div class="row">
						<div class="col-md-12">

							<div class="new-post">
								<a href="newpost.php" class="btn btn-primary btn-lg btn-block">
									<?= BBS_NEW_THREAD; ?>
								</a>
							</div>

							<div class="search">
								<form action="" method="get">
									<div class="input-group">
										<span class="input-group-addon"><?= BBS_SEARCH; ?></span>
										<input class="form-control" type="search" name="q" value="<?= h(filter_input(INPUT_GET, 'q')); ?>" />
									</div>
									<p class="text-right">
										<a href="/"><?= BBS_CLEAR; ?></a>
									</p>
								</form>
							</div>

							<div class="widget">
								widget2
							</div>

							<div class="widget">
								widget3
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="//code.jquery.com/jquery-3.1.0.min.js"></script>
		<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<script src="js/lightbox.js" type="text/javascript"></script>
	</body>
</html>