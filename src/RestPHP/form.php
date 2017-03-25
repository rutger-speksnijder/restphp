<?php
/**
 * Default form to use for the "/authorize" endpoint.
 *
 * @author Rutger Speksnijder.
 * @since RestPHP 1.0.
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT.
 */
?>
<form method="POST">
    <label>Do you authorize "<?= $this->data['client_id'] ?>"?</label>
    <br>
    <input type="submit" name="authorized" value="yes">
    <input type="submit" name="authorized" value="no">
</form>
