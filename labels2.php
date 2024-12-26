<?
    require 'assets/control.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset=utf-8 />
        <title>BuyToFill</title>
        <meta name=viewport content="width=device-width, initial-scale=1" />
        <meta name=handheldfriendly content=true />
        <meta name=MobileOptimized content=width />
        <meta name=description content=BuyToFill />
        <meta name=author content=BuyToFill />
        <meta name=keywords content=BuyToFill />
        <link rel=icon href=assets/favicon.ico />
        <link rel=stylesheet href=assets/styles.css />
        <script type="importmap">
            {
                "imports": {
                    "three":"https://unpkg.com/three@0.167.1/build/three.module.js",
                    "WebGL":"https://unpkg.com/three@0.167.1/examples/jsm/capabilities/WebGL.js",
                    "orbit":"https://unpkg.com/three@0.167.1/examples/jsm/controls/OrbitControls.js"
                }
            }
        </script>
        <style>
            body{background:var(--purp)}
            #control{display:flex;padding:0 1rem 1rem;color:#fff}
            #lcontrol{width:50%}
            #lctop{display:flex;align-items:center;justify-content:space-between;padding-right:1rem}
            #lctop h2{margin:1rem 0}
            #lctop button,#lctop a{background:#fff;cursor:pointer;color:var(--purp);font-size:1rem;font-weight:600;border-radius:.5rem;padding:.5rem;opacity:1}
            #lcmain{padding:1rem;background:var(--purb);margin-right:1rem;border-radius:.5rem;overflow-y:scroll;height:calc(100% - 63px);padding-right:.5rem}
            canvas{border-radius:1rem;background:var(--dark)}
            #rview{position:relative}
            #rview h2,#rview h3{position:absolute;top:1.5rem;right:50%;transform:translateX(50%)}
            #rview h3{position:absolute;top:3.5rem;right:50%;transform:translateX(50%)}
            svg[onclick="toggleGrid(this)"]{cursor:pointer;position:absolute;top:1.5rem;left:1rem;width:30px;height:30px}
            svg[onclick="toggleGrid(this)"].d{color:#444;border-radius:.5rem}
            
            .crOut{border-radius:.2rem;display:flex;background:#222;overflow:hidden;transition:border-radius .2s ease;opacity:.8}
            .crIn{width:100%;display:flex;cursor:pointer;transition:transform .5s ease;border:1.5px solid #0000}
            .crOut>input{padding:1rem;background:#333;width:10ch;text-align:right;margin-right:-100%;border-radius:2rem;height:35px}
            .crOut:not(:last-child){margin-bottom:.5rem}
            .crHolder{padding:.5rem;display:flex;width:50px;height:50px}
            .crHolder img{border-radius:.2rem;max-width:100%;max-height:100%;margin:auto;display:block}
            .croTop{width:100%;font-size:.9rem;margin-top:.4rem}
            .crotDesc{font-size:.87rem}
            
            .crOut:has(.crIn.s){border-top-right-radius:2rem;border-bottom-right-radius:2rem;opacity:1}
            .crOut:has(.crIn.s)>input{margin:auto .5rem auto 0}
            
            ::-webkit-scrollbar{background:#0000;padding:1rem}
            ::-webkit-scrollbar-thumb{background:#0005;border-radius:1rem}
            
            #lcmain>button{width:100%;color:var(--purp);padding:.5rem;border-radius:.25rem;font-size:1rem;font-weight:700;cursor:pointer}
            .lcmRow{display:flex}
            .lcmRow>svg{height:30px}
            
            .withoutVisualizer{height: calc(100% - 50px)}
            .withoutVisualizer #lcontrol{width:100%}
            .withoutVisualizer #rview{display:none}
            .withoutVisualizer #lctop{padding-right:0}
            .withoutVisualizer #lcmain{margin-right:0;padding: 1rem 0 1rem 1rem}
            
            #lcontrol:not(:has(input[required])) #lctop>button{opacity:.5;cursor:auto}
            
            #lcmain>.lcmRow{margin-top:1rem}
            
            .lcmRow{background:#232323;border-radius:.5rem}
            
        </style>
    </head>
    <body>
        <?require 'assets/header.php'?>
        <div id=control class=withoutVisualizer>
            <form id=lcontrol onsubmit="parseForm(this,event)">
                <div id=lctop>
                    <?=isset($_GET['dim'])?'<a href=labels2>Back</a>':''?>
                    <h2><?=isset($_GET['dim'])?'Input preferred box dimensions':'Select what you\'re sending'?></h2>
                    <button type=submit>Continue</button>
                </div>
                <div id=lcmain>
                    <? if(isset($_GET['dim'])){ ?>
                    <button>Add Dimensions</button>
                    <div class=lcmRow>
                        <h5>Box 1</h5>
                        <input type=number placeholder=Width>
                        <input type=number placeholder=Height>
                        <input type=number placeholder=Length>
                        <svg onclick="remDim(this.parentNode)">
                            <path d="m19.5 18.2-3-3 3.1-3c.5-.5.4-1.3-.1-1.8-.5-.5-1.2-.5-1.7 0l-3 3.1-3-3c-.5-.5-1.3-.6-1.8-.1-.2.2-.3.5-.3.9 0 .3.1.6.4.9l3 3-3.1 3c-.2.2-.3.5-.3.9 0 .3.1.6.3.8.5.5 1.3.6 1.8.1l3-3 2.9 3c.5.5 1.3.4 1.8-.1.5-.5.5-1.2 0-1.7"></path>
                        </svg>
                    </div>
                    <? }else{
                        $conn = new mysqli(getenv('DATABASE_HOST'), getenv('DATABASE_USER'), getenv('DATABASE_PASS'), getenv('DATABASE_NAME'));
                        $uid = $_SESSION['uid'];
                        $stmt = $conn->prepare("SELECT o.pid, i.name, i.spec, c.qty, c.id FROM `commit` AS c INNER JOIN `order` AS o ON c.oid = o.id INNER JOIN `item` AS i ON o.pid = i.id WHERE c.uid = ? AND c.status >= 0 AND c.qty > 0 ORDER BY c.created DESC");
                        $stmt->bind_param("i", $uid);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result->num_rows > 0){
                            while ($row = $result->fetch_assoc()){
                    ?>
                    <div class=crOut>
                        <label for="<?=enc($row['id'])?>" class=crIn onclick=select(this)>
                            <div class=crHolder>
                                <img src="assets/images/<?=$row['pid']?>.webp" title=Preview>
                            </div>
                            <div class=croTop>
                                <h4 class=crotName><?=$row['name']?></h4>
                                <p class=crotDesc><?=$row['spec']?></p>
                            </div>
                        </label>
                        <input type=number id="<?=enc($row['id'])?>" placeholder=Quantity min=1 max=<?=$row['qty']?>>
                    </div>
                    <?
                                }
                            }
                        }
                    ?>
                </div>
            </form>
            <div id=rview>
                <h2>Label Visualizer</h2>
                <h3>1/1</h3>
                <svg onclick=toggleGrid(this) stroke=currentColor>
                    <path stroke-width=2 stroke-linecap=round stroke-linejoin=round d="m24 11.2v-4c0-1.3-1.1-2.4-2.4-2.4h-4m6.4 6.4h-6.4m6.4 0v6.4m-19.2-6.4v-4c0-1.3 1.1-2.4 2.4-2.4h4m-6.4 6.4h6.4m-6.4 0v6.4m12.8-6.4h-6.4m6.4 0v-6.4m0 6.4v6.4m-6.4-6.4v-6.4m0 6.4v6.4m12.8 0v4c0 1.3-1.1 2.4-2.4 2.4h-4m6.4-6.4h-6.4m-12.8 0v4c0 1.3 1.1 2.4 2.4 2.4h4m-6.4-6.4h6.4m6.4 0h-6.4m6.4 0v6.4m-6.4-6.4v6.4m0-19.2h6.4m-6.4 19.2h6.4"/>
                </svg>
            </div>
        </div>
        <script>
            function parseForm(a, e) {
                e.preventDefault();
                const send = [...a.querySelectorAll('input[required]')].map(b => [b.id, b.value]);
                if(send.length){
                    localStorage.setItem('labelContent', JSON.stringify(send));
                    window.location.href = 'labels2?dim';
                }
            }

            function select(a){
                const b = a.parentNode;
                if(a.classList.contains('s')){
                    a.classList.remove('s');
                    b.childNodes[3].removeAttribute('required');
                    b.innerHTML = b.innerHTML;
                }else{
                    a.classList.add('s');
                    b.childNodes[3].setAttribute('required',1);
                }
            }
        </script>
        <script type="module">
            import * as THREE from "three";
            import { OrbitControls } from "orbit";
            import WebGL from "WebGL";

            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(100, window.innerWidth/2 / window.innerHeight, 0.1, 1000);
        
            const renderer = new THREE.WebGLRenderer({antialias:1});
            renderer.setSize(window.innerWidth/2, window.innerHeight-65);
            renderer.setClearColor(0x111111);
            renderer.setPixelRatio(window.devicePixelRatio);
            document.getElementById("rview").appendChild(renderer.domElement);
        
            const geometry = new THREE.BoxGeometry(5, 5, 5);
            //const material = new THREE.MeshBasicMaterial({ color: 0x333333 });
            //const cube = new THREE.Mesh(geometry, material);
            //scene.add(cube); 
        
            const edges = new THREE.EdgesGeometry(geometry);
            const lineMaterial = new THREE.LineBasicMaterial({color:0xffffff});
            const line = new THREE.LineSegments(edges,lineMaterial);
            line.position.y = 2.5;
            scene.add(line);
            
            camera.position.set(0,7,10);
            camera.lookAt(0,5,0);
                
            const controls = Object.assign(new OrbitControls(camera, renderer.domElement), {enableDamping: 1, dampingFactor: 0.25, enableZoom: 1, enablePan: 0});
  
            function animate(){
                requestAnimationFrame(animate);
                controls.update();
                renderer.render(scene, camera);
            }
            
            let grid = new THREE.GridHelper(10, 10);
            scene.add(grid);
            window.toggleGrid = (a) => {
                if(grid){
                    a.classList = "d";
                    scene.remove(grid);
                    grid = null;
                }else{
                    a.classList = "";
                    grid = new THREE.GridHelper(10, 10);
                    scene.add(grid);
                }
                renderer.setAnimationLoop(animate);
            }
            
            if(WebGL.isWebGL2Available()) renderer.setAnimationLoop(animate);
            else alert(WebGL.getWebGL2ErrorMessage());
        </script>
    </body>
</html>