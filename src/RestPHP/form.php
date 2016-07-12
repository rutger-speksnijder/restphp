<?php
/**
 * Form
 *
 * An example form to use for the authorization endpoint.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.0
 * @version 1.0
 * @package RestPHP
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
?>
<form method="POST">
    <label>Do you authorize "<?= $this->data['client_id'] ?>"?</label>
    <br>
    <input type="submit" name="authorized" value="yes">
    <input type="submit" name="authorized" value="no">
</form>
