<?php
/**
 * The Template for displaying project archives, including the main showcase page which is a post type archive.
 *
 * @package WordPress
 * @subpackage Outstock_Themes
 * @since Outstock Themes 1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $projects_loop, $post, $outstock_projectrows, $outstock_opt, $wp_query;
$project_setting = get_option( 'projects-pages-fields' );
$page_id = (!empty($project_setting['projects_page_id'])) ? absint($project_setting['projects_page_id']) : 0;
get_header( 'projects' ); ?>

<div id="main-content" class="main-container">
	<?php if(get_post_meta( $page_id, 'lionthemes_page_banner', true )){ ?>
		<?php do_action( 'lionthemes_page_banner' ); ?>
	<?php } else { ?>
	<div class="entry-header">
		<div class="container">
			<h1 class="entry-title"><?php esc_html_e('Portfolio', 'outstock');?></h1>
		</div>
	</div>
	<?php } ?>
	<div class="container">
	
		<div class="page-content">
			
			<?php do_action( 'projects_archive_description' ); ?>

			<?php
			$projects_per_page = 10;
			if( isset($outstock_opt['portfolio_per_page']) ) {
				$projects_per_page = $outstock_opt['portfolio_per_page'];
			}
			$projects_args = $wp_query->query_vars;
			
			$paged = get_query_var( 'paged', 1 );
			
			$projects_args['post_type'] = 'project';
			$projects_args['posts_per_page'] = $projects_per_page;
			$projects_args['paged'] = $paged;
			
			if(!isset($wp_query->query["project-category"])){ //if is not the category page
				$projects_args = array(
					'posts_per_page' => $projects_per_page,
					'post_type' => 'project',
					'paged' => $paged,
					'nopaging' => false
				);
			}
			//var_dump($projects_args);
			
			$projects_query = new WP_Query( $projects_args );
			?>
				
			<?php if ( $projects_query->have_posts() ) : ?>

				<?php
					/**
					 * projects_before_loop hook
					 *
					 */
					do_action( 'projects_before_loop' );
				?>
				<div class="filter-options btn-group">
					<button data-group="all" class="btn active btn-warning"><?php esc_html_e('All', 'outstock');?></button>
					<?php 
					$datagroups = array();
					
					while ( $projects_query->have_posts() ) : $projects_query->the_post();
					
						$prcates = get_the_terms($post->ID, 'project-category' );
						
						if($prcates) {
							foreach ($prcates as $category ) {
								$datagroups[$category->slug] = $category->name;
							}
						}
						?>
					<?php endwhile; // end of the loop. ?>
					<?php
					foreach($datagroups as $key=>$value) { ?>
						<button data-group="<?php echo esc_attr($key);?>" class="btn btn-warning"><?php echo esc_html($value);?></button>
					<?php }
					?>
				</div>
				<div class="list_projects entry-content">

				<?php projects_project_loop_start(); ?>
					<?php $outstock_projectrows = 1; ?>
					<?php while ( $projects_query->have_posts() ) : $projects_query->the_post(); ?>

						<?php projects_get_template_part( 'content', 'project' ); ?>

					<?php endwhile; // end of the loop. ?>

				<?php projects_project_loop_end(); ?>
				
				</div><!-- .projects -->

				<?php
					/**
					 * projects_after_loop hook
					 *
					 * @hooked projects_pagination - 10
					 */
					do_action( 'projects_after_loop' );
				?>

			<?php else : ?>

				<?php projects_get_template( 'loop/no-projects-found.php' ); ?>

			<?php endif; ?>

			<?php wp_reset_postdata(); ?>
			
		</div>
	</div>
</div>
<?php get_footer( 'projects' ); ?>