<ol>
    <a href="dashboard">Dashboard</a>
    <? if($page != "dashboard"){ ?>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" focusable="false" aria-hidden="true"><path d="m4 1 7 7-7 7"></path></svg>
    <a><?echo $page?></a>
    <? } ?>
</ol>