<div id="globNotif" onclick="hideNotif(this)">Placeholder</div>
<script>
    function sendNotif(a,s){
        var notif = document.querySelector('#globNotif');
        function handleTransitionEnd(){
            let bg;
            if(s){
                switch(s){
                    case 200:
                        bg = "green";
                        break;
                    case 400:
                        bg = "red";
                        break;
                    default: 
                        bg = "tint";
                        notif.style.color = "white"
                }
                notif.style.background = "var(--" + bg + ")";
            }
            if(a) notif.textContent = a;
            notif.style.transform = "translateX(0)";
            notif.removeEventListener('transitionend', handleTransitionEnd);
            
            setTimeout(function() {
                notif.style.transform = "translateX(calc(100% + 1rem + 4px))";
            }, 5000);
        }
        if(notif.style.transform == "translateX(0)"){
            notif.style.transform = "translateX(calc(100% + 1rem + 4px))";
            notif.addEventListener('transitionend', handleTransitionEnd);
        }else handleTransitionEnd();
    }
    function hideNotif(a){
        a.style.transform = "translateX(calc(100% + 1rem + 4px))";
    }
</script>