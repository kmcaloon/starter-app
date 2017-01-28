<?php
/**
 * WYSIWYG Widget
 */
namespace Starter;

class WYSIWYG extends \WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {

        parent::__construct(
            'wysiwyg_widget', // Base ID
            __( 'WYSIWYG', 'starter-theme' ), // Name
            array( 'description' => __( 'WYSIWYG Editor', 'starter-theme' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {

        $widgets = \Starter\Settings::dir( 'widgets' );
        $widget_id = $args['widget_id'];

        include( locate_template( $widgets . '/wysiwyg.php' ) ); 
    
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( '', 'sage' );
        ?>
        <!-- <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p> -->
        <?php 
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }

} 
add_action( 'widgets_init', function(){
    register_widget( __NAMESPACE__ . '\\WYSIWYG' );
});