<?php


class RRTNGG_Field_Generator {
	public static function form_field( $id, $field, $value = '' ) {
		$type = ! empty( $field['type'] ) ? $field['type'] : '';

		if ( 'html' === $type ) {
			$html = ! empty( $field['html'] ) ? $field['html'] : '';
			return $html;
		}

		if ( isset( $field['disable_label_key'] ) ) {
				$label = ! empty( $field['label'] ) ? sprintf( '<label class="%s">%s</label>', $id, $field['label'] ) : '';
		} else {
			$label = ! empty( $field['label'] ) ? sprintf( '<label for="%s">%s</label>', $id, $field['label'] ) : '';
		}

		$type = ! empty( $field['type'] ) ? $field['type'] : 'text';

		switch ( $type ) {
			case 'wpeditor':
				$input = self::wpeditor_field( $id, $value, $field );
				break;
			case 'select':
				$input = self::select_field( $id, $value, $field );
				break;
			case 'textarea':
				$input = self::textarea_field( $id, $value, $field );
				break;
			case 'hidden':
				$input = self::hidden_field( $id, $value, $field );
				break;
			case 'checkbox':
				$input = self::checkbox_field( $id, $value, $field );
				break;
			default:
				$input = self::text_field( $id, $value, $field );
		}

		if ( 'hidden' === $type ) {
			return $input;
		} else {
			return self::format_rows( $label, $input, $field );
		}
	}

	public static function wpeditor_field( $id, $value, $field = array() ) {
		$settings = array(
			'wpautop' => true, // use wpautop - add p tags when they press enter
			'teeny'   => false, // output the minimal editor config used in Press This
			'tinymce' => array(
				'height' => '250', // the height of the editor
			),
		);

		if ( empty( $value ) && empty( $field['allow_empty'] ) && ! empty( $field['default'] ) ) {
			$value = $field['default'];
		}

		ob_start();
		wp_editor( stripcslashes( $value ), $id, $settings );
		return ob_get_clean();
	}

	public static function select_field( $id, $value, $field = array() ) {
		if ( empty( $field['options'] ) ) {
			return '';
		}

		$default_value  = ! empty( $field['default'] ) ? $field['default'] : '';
		$allowed_values = array_keys( $field['options'] );

		if ( ! in_array( $value, $allowed_values ) ) {
			$value = $default_value;
		}
		$attributes = self::get_field_attributes( $field );
		$name       = ! empty( $field['name'] ) ? $field['name'] : $id;

		ob_start();
		?>
		<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>"<?php echo wp_kses( $attributes, 'strip' ); ?>>
			<?php
			foreach ( $field['options'] as $val => $label ) {
				?>
				<option value="<?php echo esc_html( $val ); ?>"<?php echo $val === $value ? ' selected' : ''; ?>><?php echo esc_html( $label ); ?></option>
				<?php
			}
			?>
		</select>
		<?php
		return ob_get_clean();
	}

	public static function hidden_field( $id, $value, $field = array() ) {
		$attributes = self::get_field_attributes( $field );
		$name       = ! empty( $field['name'] ) ? $field['name'] : $id;

		return sprintf(
			'<input id="%s" name="%s" type="hidden" value="%s" %s>',
			esc_attr( $id ),
			esc_attr( $name ),
			$value,
			$attributes
		);
	}

	public static function text_field( $id, $value, $field = array() ) {
		$type       = ! empty( $field['type'] ) ? $field['type'] : 'text';
		$attributes = self::get_field_attributes( $field );
		$name       = ! empty( $field['name'] ) ? $field['name'] : $id;

		return sprintf(
			'<input id="%s" name="%s" type="%s" value="%s" %s>',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $type ),
			esc_html( $value ),
			$attributes
		);
	}

	public static function checkbox_field( $id, $value, $field = array() ) {
		$attributes = self::get_field_attributes( $field );
		$name       = ! empty( $field['name'] ) ? $field['name'] : $id;

		if ( 'on' != $value && $value != '1' ) {
			$value = '';
		}
		$checked = '';
		if ( ! empty( $value ) ) {
			$checked = ' checked';
		}

		return sprintf(
			'<input id="%s" name="%s" type="checkbox" %s %s>',
			esc_attr( $id ),
			esc_attr( $name ),
			$checked,
			$attributes
		);
	}

	public static function textarea_field( $id, $value, $field = array() ) {
		$type       = ! empty( $field['type'] ) ? $field['type'] : 'text';
		$attributes = self::get_field_attributes( $field );
		$name       = ! empty( $field['name'] ) ? $field['name'] : $id;

		return sprintf(
			'<textarea id="%s" name="%s"%s>%s</textarea>',
			esc_attr( $id ),
			esc_attr( $name ),
			$attributes,
			esc_textarea( $value )
		);
	}

	public static function get_field_attributes( $field ) {
		$attributes = '';

		if ( ! empty( $field['classes'] ) ) {
			$classes = $field['classes'];
			if ( is_array( $classes ) ) {
				$classes = implode( ' ', $classes );
			}
			$attributes .= ' class="' . esc_attr( $classes ) . '"';
		}

		if ( ! empty( $field['step'] ) ) {
			$attributes .= ' step="' . esc_attr( $field['step'] ) . '"';
		}

		$datas = array();

		if ( ! empty( $field['data'] ) ) {
			foreach ( $field['data'] as $id => $val ) {
				$datas[] = 'data-' . esc_attr( $id ) . '="' . esc_attr( $val ) . '"';
			}
		}

		if ( ! empty( $datas ) ) {
			$attributes .= ' ' . implode( ' ', $datas );
		}

		return $attributes;
	}

	public static function get_container_attributes( $field ) {
		$attributes = '';

		if ( ! empty( $field['container_classes'] ) ) {
			$classes = $field['container_classes'];
			if ( is_array( $classes ) ) {
				$classes = implode( ' ', $classes );
			}
			$attributes .= ' class="' . esc_attr( $classes ) . '"';
		}

		$datas = array();

		if ( ! empty( $field['container_data'] ) ) {
			foreach ( $field['container_data'] as $id => $val ) {
				$datas[] = 'data-' . esc_attr( $id ) . '="' . esc_attr( $val ) . '"';
			}
		}

		if ( ! empty( $datas ) ) {
			$attributes .= ' ' . implode( ' ', $datas );
		}

		return $attributes;
	}

	public static function container_attributes( $field ) {
		if ( ! empty( $field['container_classes'] ) ) {
			$classes = $field['container_classes'];
			if ( is_array( $classes ) ) {
				$classes = implode( ' ', $classes );
			}
			echo ' class="' . esc_attr( $classes ) . '"';
		}

		if ( ! empty( $field['container_data'] ) ) {
			foreach ( $field['container_data'] as $id => $val ) {
				echo ' data-' . esc_attr( $id ) . '="' . esc_attr( $val ) . '"';
			}
		}
	}

	public static function format_rows( $label, $input, $field = array() ) {
		$attributes        = self::get_container_attributes( $field );
		$short_description = ! empty( $field['short_description'] ) ? $field['short_description'] : '';
		$description       = ! empty( $field['description'] ) ? $field['description'] : '';
		ob_start();

		if ( empty( $field['row_template'] ) || ! file_exists( RRTNGG_ABS_PATH . 'templates/fields/' . $field['row_template'] . '.php' ) ) {
			?>
			<tr<?php self::container_attributes( $field ); ?>>
				<th scope="row"><?php echo wp_kses( $label, RRTNGG_Manager::get_allowed_tags() ); ?></th>
				<td>
					<?php
					$allowed_tags = RRTNGG_Manager::get_fields_allowed_tags();

					if ( ! empty( $field['allowed_tags'] ) ) {
						if ( is_array( $field['allowed_tags'] ) ) {
							$allowed_tags = array_merge( $allowed_tags, $field['allowed_tags'] );
						} else {
							$allowed_tags = $field['allowed_tags'];
						}
					}

					echo wp_kses( $input, $allowed_tags );

					if ( ! empty( $short_description ) ) {
						?>
						<p><?php echo wp_kses( $short_description, RRTNGG_Manager::get_allowed_tags() ); ?></p>
						<?php
					}

					if ( ! empty( $description ) ) {
						echo wp_kses( $description, RRTNGG_Manager::get_allowed_tags() );
					}
					?>
				</td>
			</tr>
			<?php
		} else {
			include RRTNGG_ABS_PATH . 'templates/fields/' . $field['row_template'] . '.php';
		}
		?>
		<?php
		return ob_get_clean();
	}
}
