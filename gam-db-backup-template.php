<?php
/*
* This file use for setings at admin site for GAM DB Backup.
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * GAM_DB_Backup_Settings class.
 */
class GAM_DB_Backup_Settings {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->settings_group = 'db_backup';
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * init_settings function.
	 *
	 * @access protected
	 * @return void
	 */
	protected function init_settings() {
		// Prepare roles option
		$this->settings = apply_filters( 'db_backup_settings',
			array(
				'db_backup_details' => array(
					__( 'Database Detail', 'gam-db-backup' ),
					array(

						array(
							'name'       => 'gam_db_backup_host_name',
							'std'        => DB_HOST,
							'label'      => __( 'Host name', 'gam-db-backup' ),							
							'desc'       => __( '', 'gam-db-backup' ),
							'type'       => 'label',
							'attributes' => array()
						),
						array(
							'name'       => 'gam_db_backup_db_name',
							'std'        => DB_NAME,
							'label'      => __( 'Database name', 'gam-db-backup' ),							
							'desc'       => __( '', 'gam-db-backup' ),
							'type'       => 'label',
							'attributes' => array()
						),
						array(
							'name'       => 'gam_db_backup_user_name',
							'std'        => DB_USER,
							'label'      => __( 'Database User name', 'gam-db-backup' ),
							'desc'       => __( '', 'gam-db-backup' ),
							'type'       => 'label',
							'attributes' => array()
						),
						array(
							'name'       => 'gam_db_backup_user_password',
							'std'        => '******',
							'label'      => __( 'Password', 'gam-db-backup' ),
							'desc'       => __( '', 'gam-db-backup' ),
							'type'       => 'label',
							'attributes' => array()
						),
						
					),
				),
				'db_backup_now' => array(
					                            __( 'Backup Now', 'gam-db-backup' ),
					                            array(
						                            array(
							                            'name' 		=> 'gam_db_backup_start_backup',
							                            'std' 		=> 'Backup',
							                            'label' 	=> __( 'Backup', 'gam-db-backup' ),
										    'value'     => 'Start Backup',
										     'id'        =>'gam_db_backup_start_backup',  							
							                            'type'      => 'button'
							
						                            ),
						                            array(
							                            'name' 		=> 'gam_db_backup_download_file',
							                            'std' 		=> '',
							                            'label' 	=> __( 'Download', 'gam-db-backup' ),
										    'value'     => 'Download File',
										     'id'        =>'gam_db_backup_download_file',  							
							                            'type'      => 'anchor'
							
						                            )
					                             )
			 	),
				'db_backup_schedules' => array(
								   __( 'Schedule Setting', 'gam-db-backup' ),
					                            array(
							                             array(
		                                                        			'name'       => 'gam_db_backup_schedules_options',		                                                        			
		                                                        			'std'        => '',
								                                'label'    => __('Auto Backup Frequency', 'gam-db-backup' ),
								                                'type'     => 'radio',
								                                'required' => false,						                               
								                                'priority' => 1,
								                                'options'  => array(
								                                                          'daily' => __( 'Once Daily', 'gam-db-backup' ),
								                                                          'weekly' => __( 'Once Weekly', 'gam-db-backup' ),
								                                                          'monthly' =>__('Once Monthly', 'gam-db-backup' ),
								                                                          'none' =>__('No Schedule', 'gam-db-backup' )  
								                                                    )
											  ),
											 array(
													'name'       => 'gam_db_backup_schedules_options_save',
													'id'        =>'gam_db_backup_schedules_options_save',  
													'std' 		=> '',
													'label' 	=> '',
													'value'     =>__( 'Save', 'gam-db-backup' ),													
 													'type'      => 'button'
									
												)				
									
										)				

				               				 )
				
				
			)
		);
	}

	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {
		$this->init_settings();

		foreach ( $this->settings as $section ) {
			foreach ( $section[1] as $option ) {
				if ( isset( $option['std'] ) )
					add_option( $option['name'], $option['std'] );
				register_setting( $this->settings_group, $option['name'] );
			}
		}
	}

	/**
	 * output function.
	 *
	 * @access public
	 * @return void
	 */
	public function output() {
		$this->init_settings();
		
?>
			    <h2 class="nav-tab-wrapper">
                
              
			    	<?php
			    		foreach ( $this->settings as $key => $section ) {
			    			echo '<a href="#settings-' . sanitize_title( $key ) . '" class="nav-tab">' . esc_html( $section[0] ) . '</a>';
			    		}
			    	?>
			    </h2>
			    <?php
				foreach ( $this->settings as $key => $section ) {

						echo '<div id="settings-' . sanitize_title( $key ) . '" class="settings_panel">';

						echo '<table class="form-table">';

						foreach ( $section[1] as $option ) {

							$placeholder    = ( ! empty( $option['placeholder'] ) ) ? 'placeholder="' . $option['placeholder'] . '"' : '';
							$class          = ! empty( $option['class'] ) ? $option['class'] : '';
							$value          = get_option( $option['name'] );
							$option['type'] = ! empty( $option['type'] ) ? $option['type'] : '';
							$attributes     = array();

							if ( ! empty( $option['attributes'] ) && is_array( $option['attributes'] ) )
								foreach ( $option['attributes'] as $attribute_name => $attribute_value )
									$attributes[] = esc_attr( $attribute_name ) . '="' . esc_attr( $attribute_value ) . '"';

							echo '<tr valign="top" class="' . $class . '"><th scope="row"><label for="setting-' . $option['name'] . '">' . $option['label'] . '</a></th><td>';

							switch ( $option['type'] ) {
								
								case "" :
								case "input" :
								case "label" :

									?> <label for="<?php esc_attr_e( $option['name'] ); ?>"> : <?php echo $option['std']; ?></label><?php

									if ( $option['desc'] ) {
										echo ' <p class="description">' . $option['desc'] . '</p>';
									}

								break;
								
								case "button" :							
							 
										?> <input type="submit" name="<?php echo $option['name'] ?>" value="<?php echo $option['value']; ?>" id="<?php echo $option['id']; ?>" /><?php					
								
                              					break;	
                              					case "anchor" :							
							 
										?> <a  id="<?php echo $option['id']; ?>" href="" target="_blank" download><?php echo $option['value']; ?></a><?php					
								
                              					break;						

								


								case "radio" :
								
								    $selected_schedule_option = get_option('gam_db_backup_schedules_options');
								   
							
								    foreach($option['options'] as $key=>$value)
								    {								    
								    	?>
                                		                    <input type="radio" name="<?php echo $option['name']; ?>" id="<?php echo $option['name'] .  '_' . $key; ?>" value="<?php echo $key; ?>" <?php if($selected_schedule_option == $key){ echo 'checked="checked" '; } ?> /> <?php echo $value ; 
						
								    }								    
								     if ( !empty($selected_schedule_option) && $selected_schedule_option !='none') 
								    { 
								        echo "<br/><br/><p class='description'>";
									printf( __( 'For once %s   : database will store at  wp-contents/uploads/gam-db-backup/%s/', 'gam-event-manager-email' ), $selected_schedule_option, $selected_schedule_option  ); 
									echo "</p>";   
								    }	
                              					break;
                              					
								default :
									do_action( 'gam_db_backup_admin_field_' . $option['type'], $option, $attributes, $value, $placeholder );
								break;

							}

							echo '</td></tr>';
						}

						echo '</table></div>';

					}
				?>
				
		  
		</div>		
   
	
		<?php

	}
}