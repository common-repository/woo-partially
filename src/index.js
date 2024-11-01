import { decodeEntities } from '@wordpress/html-entities';
import { __ } from '@wordpress/i18n';

const { registerPaymentMethod } = window.wc.wcBlocksRegistry
const { getSetting } = window.wc.wcSettings

const settings = getSetting( 'partially_data', {} )

const label = decodeEntities( settings.title )

const Content = () => {
	return decodeEntities( settings.description || '' )
}

const Label = () => {
    return (
        <span style={{ width: '100%' }}>
            {label}
            <img src={settings.icon} style={{ float: 'right', marginRight: '10px' }} />
        </span>
    )
}

registerPaymentMethod( {
	name: "partially",
	label: <Label />,
	content: <Content />,
	edit: <Content />,
	canMakePayment: () => true,
	ariaLabel: label,
    placeOrderButtonLabel: __( 'Proceed to Partial.ly', 'woo_partially' ),
	supports: {
		features: settings.supports,
	}
} )