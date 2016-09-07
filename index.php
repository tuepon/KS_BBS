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
$rs = $res['rows'];
var_dump($rs);
?>
<!DOCTYPE HTML>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title>掲示板サンプル</title>
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
				<h1 class="blog-title">掲示板サンプル</h1>
				<p class="lead blog-description">ここに掲示板の説明を記述します。</p>
			</div>

			<div class="order">
				<span>ソート</span>
				<a href="?order=new">新しいもの順</a>
				<a href="?order=comment">返信の多い順</a>
			</div>

			<div class="row">

				<div class="col-sm-8 blog-main">

					<?php if (isset($rs) && count($rs) > 0) : ?>

						<div class="threads">

							<?php foreach ($rs as $r) : ?>

								<div class="thread" data-id="<?= h($r['id']) ?>">

									<h2><?= h($r['title']) ?></h2>

									<div class="username">
										Post by <?= h($r['username']) ?> at <?= h($r['create_at']) ?>
									</div>

									<div class="comment">
										<?= nl2br(h($r['comment'])) ?>
									</div>

									<div class="comment-footer">
										<a href="reply.php?id=<?= h($r['id']) ?>">[ 返信する ]</a>
										<span>返信数: <?= h($r['comment_count']) ?></span>
									</div>

									<?php if (isset($r['reply']['count']) && $r['reply']['count'] > 0) : ?>

										<?php foreach ($r['reply'] as $i => $reply) : ?>

											<div class="reply">
												<h3>#<?= ($i + 1); ?> Re: <?= h($r['title']) ?></h3>

												<div class="username">
													Post by <?= h($reply['username']) ?> at <?= h($reply['create_at']) ?>
												</div>

												<div class="comment">
													<?= nl2br(h($reply['comment'])) ?>
												</div>
											</div>

										<?php endforeach; ?>

									<?php endif; ?>

								</div>

							<?php endforeach; ?>

						</div>

						<div class="row pagination_nav">
							<div class="col-xs-12">
								<?php echo pagination(filter_input(INPUT_GET, 'page'), $res['count']); ?>
							</div>

						</div>

					<?php endif; ?>

				</div>
				<div class="col-sm-4">

					<div class="row">
						<div class="col-md-12">

							<div class="new-post">
								<a href="newpost.php" class="btn btn-primary btn-lg btn-block">
									新規スレッド投稿
								</a>
							</div>

							<div class="search">
								<form action="" method="get">
									<div class="input-group">
										<span class="input-group-addon">検索</span>
										<input class="form-control" type="search" name="q" value="<?= h(filter_input(INPUT_GET, 'q')); ?>" />
									</div>
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
		<script type="text/javascript" src="//code.jquery.com/jquery-3.0.0.min.js"></script>
		<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>