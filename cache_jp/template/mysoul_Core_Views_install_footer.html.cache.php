

</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
    <?php $bg = array('"'.THEME_PATH.'assets/pages/media/bg/1.jpg"',
            '"'.THEME_PATH.'assets/pages/media/bg/2.jpg"',
            '"'.THEME_PATH.'assets/pages/media/bg/3.jpg"',
            '"'.THEME_PATH.'assets/pages/media/bg/4.jpg"');
    shuffle($bg);
    ?>
    jQuery(document).ready(function() {
        $.backstretch([
            <?php echo implode(',', $bg); ?>
        ], {
            fade: 1000,
                    duration: 8000
        }
        );
    });
</script>
</body>
</html>