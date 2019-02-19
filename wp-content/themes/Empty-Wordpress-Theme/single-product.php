<?php get_header(); ?>
<section>
    <div class="container">
		<div class="content item">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
				<div class="row">
                    <div class="col-sm-12">
                        <h2><?php the_title(); ?></h2>
                    </div>
                </div>

				<div class="row">
                    <div class="col-sm-6">
						<div class="type">
							Тип: <?php $type = wp_get_post_terms( get_the_ID(), 'type', array() );
							echo $type[0]->name
							?>
						</div>
				</div>
				<div class="col-sm-6 align-right">
						<div class="date">
							Дата проведения: <?php echo get_post_meta(get_the_ID(), "date", 1); ?>
						</div>

                    </div>
                </div>

				<div class="row">

					<div class="col-sm-4">

						<?php /*******************************************************************************/
						// TODO:  Следующий div выводить только при наличии тизера
						/*******************************************************************************/ ?>
						<div class="row">
							<div class="col-sm-12">
								<div class="teaser">
									<iframe width="310" height="240" src="<?php echo get_post_meta(get_the_ID(), "teaser", 1); ?>" frameborder="0" allowfullscreen></iframe>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-12">
								<div class="preview">
									<p>Превью: </p>
									<?php the_post_thumbnail(array(300,300)); ?>
								</div>
							</div>
						</div>

                    </div>

					<div class="col-sm-8">
                        <div class="row">
							<div class="col-sm-12">
								<div class="desc">
									<p>Описание: </p>
									<?php the_content(); ?>
								</div>
							</div>
						</div>

						<hr>

						<div class="row">
							<div class="col-sm-6">
								Стоимость:
								<?php echo get_post_meta(get_the_ID(), "price", 1); ?>
							</div>
							<div class="col-sm-6 align-right">
								<div class="buy">
									[ Купить ]
								</div>
							</div>
						</div>

                    </div>

                </div>
				
				<hr>

				<?php /*******************************************************************************/
				      // TODO:  Ссылка на файл (товар) должна быть не прямая, а через скрипт, проверяющий авторизацию и доступ (наличие покупки), что бы нельзя было выолжить прямую ссылку на скачивание. + если это видео - в плеере (просмотр на странице товара).
					  /*******************************************************************************/ ?>
				<div class="row">
					<div class="col-sm-12">
						<div class="teaser">
							<?php  // retrieve all Attachments for the 'attachments' instance of post 123
								$attachments = new Attachments('my_attachments', get_the_ID());
								$data = $attachments->get_attachments();
								if(!empty($data)){
									$data = [];
									while ($attachments->get()) :
										$data[] = array(
											'url' => $attachments->url(),
											'title' => $attachments->field('title'),
											'caption' => $attachments->field('caption'),
										);
									endwhile;
								}
								foreach($data as $file){
									echo "<a href='".$file['url']."'>".$file['title']."</a>";
								}
							?>
						</div>
					</div>
				</div>

				<br><br><br>



				<?php /*
				<div class="row">
					<p>Заголовок: <?php the_title(); ?></p>

					<p>Описание: </p>
					<?php the_content(); ?>


					<p>Статус: </p>
					<?php echo get_post_status(get_the_ID()); ?>
					<p>Файл: </p>
					   <?php  // retrieve all Attachments for the 'attachments' instance of post 123
						$attachments = new Attachments('my_attachments', get_the_ID());
						$data = $attachments->get_attachments();
						if(!empty($data)){
							$data = [];
							while ($attachments->get()) :
								$data[] = array(
									'url' => $attachments->url(),
									'title' => $attachments->field('title'),
									'caption' => $attachments->field('caption'),
								);
							endwhile; }
					   //prn($data);
					   foreach($data as $file){
						   echo "<a href='".$file['url']."'>".$file['title']."</a>";
					   }
					   ?>
					<p>Раздел: </p>
					<?php $section = wp_get_post_terms( get_the_ID(), 'section', array() );
					//prn($terms);
					echo $section[0]->name;
					?>
					<p>Тизер: </p>

					<p>Признак: </p>
					<?php echo get_post_meta(get_the_ID(), "mark", 1); ?>
					<p>Стоимость: </p>
					<?php echo get_post_meta(get_the_ID(), "price", 1); ?>
					<p>Дата проведения: </p>
					<?php echo get_post_meta(get_the_ID(), "date", 1); ?>
					<p>Массив ID, входящих в набор: </p>
					<?php echo get_post_meta(get_the_ID(), "set", 1); ?>
					<p>Массив ID рекомендованых товаров: </p>
					<?php echo get_post_meta(get_the_ID(), "recommend", 1); ?>
				</div>
				*/ ?>

			<?php endwhile; ?>
			<?php endif; ?>
		</div>
    </div>
</section>
<?php get_footer(); ?>
