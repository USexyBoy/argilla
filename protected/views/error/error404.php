<?php
/**
 * @var ErrorController $this
 */
?>
<section id="main">

  <?php $this->renderPartial('/breadcrumbs');?>

  <h2 class="m7">Ошибка 404</h2>

  <div class="error">
    <p class="bb"><?php echo CHtml::encode($this->errorMessage); ?></p>
  </div>

</section>