<?php



/**
 * @author Nicolaas [at] sunnysideup.co.nz
 */


class NewsletterSignupDecorator extends DataObjectDecorator {

	function extraStatics() {
		return array(
			"db" => array(
				"NewsletterSignup" => "Boolean"
			)
		);
	}

}
