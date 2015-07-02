@extends('templates/page_main')

@section('content')
    <form id="upload">
        <div id="drop">
            <?php
            if (!empty($errorMessage)) {
                echo '
                    <div class="error">
                        ' . $errorMessage . '
                    </div>';
            }
            ?>
            <input type="file" name="userfile"/>
            <br>
        </div>
    </form>
@stop