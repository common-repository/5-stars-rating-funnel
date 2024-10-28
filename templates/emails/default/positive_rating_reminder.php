<?php
defined( 'ABSPATH' ) || exit;
?>

<p>
    <?php _e( 'Hello {lead_title} {lead_lastname},', '5-stars-rating-funnel' ); ?>
</p>

<p>
    <?php _e( 'We are pleased that we have met your expectations with our service / products.', '5-stars-rating-funnel' ); ?>
</p>

<p>
    <?php _e( 'Your positive feedback encourages us to further expand our quality standards in order to continue to offer the best possible service in the future.', '5-stars-rating-funnel' ); ?>
</p>

<p>
    <?php _e( 'A while ago, we created a Google My business entry. We would be very happy, if you could rate our company there and describe your experiences.', '5-stars-rating-funnel' ); ?>
</p>

<p>
    <a href="{review_mybusiness_url}"><?php _e( '>> Rate on Google <<', '5-stars-rating-funnel' ); ?></a>
</p>

<p>
    <?php _e( 'If you do not have a Google account, you are also welcome to review us on Facebook.', '5-stars-rating-funnel' ); ?>
</p>

<p>
    <a href="{review_custom_link_url}"><?php _e( '>> Rate on Facebook <<', '5-stars-rating-funnel' ); ?></a>
</p>

<p>
    <?php _e( 'Here are some topic ideas for your review:', '5-stars-rating-funnel' ); ?>
</p>

<p>
    - <?php _e( 'Your experience with us as a company', '5-stars-rating-funnel' ); ?><br>
    - <?php _e( 'the quality of our products / services', '5-stars-rating-funnel' ); ?><br>
    - <?php _e( 'special moments and incidents and how we have helped you', '5-stars-rating-funnel' ); ?>
</p>

<p>
    <?php _e( 'Thank you in advance for your support!', '5-stars-rating-funnel' ); ?>
</p>

<p>
    <?php _e( 'With kind regards', '5-stars-rating-funnel' ); ?>
</p>

<?php require RRTNGG_ABS_PATH . 'templates/emails/default/email_footer.php'; ?>
