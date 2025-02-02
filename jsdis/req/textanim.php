<style>
.perspective-text {
  position: absolute;
  top: 25%;
  left: 20%;
  transform: translate(-50%, 0%);
  font-family: Arial;
  font-size: 58px;
  font-weight: 900;
  letter-spacing: -2px;
  text-transform: uppercase;
  white-space: nowrap;
  color: transparent;
  -webkit-text-stroke: 2px #eeeeee;
}

.perspective-line {
  height: 50px;
  overflow: hidden;
  position: relative;
}
.perspective-line p {
  margin: 0;
  height: 50px;
  line-height: 50px;
  transition: all 0.5s ease-in-out;
}
.perspective-line:nth-child(odd) {
  transform: skew(60deg, -30deg) scaleY(0.667);
}
.perspective-line:nth-child(even) {
  transform: skew(0deg, -30deg) scaleY(1.333);
}
.perspective-line {
  position: relative;
}
.perspective-line:nth-child(1) {
  left: 29px;
}
.perspective-line:nth-child(2) {
  left: 58px;
}
.perspective-line:nth-child(3) {
  left: 87px;
}
.perspective-line:nth-child(4) {
  left: 116px;
}
.perspective-line:nth-child(5) {
  left: 145px;
}

/* Sliding text on all lines */
.perspective-text p{
  transform: translate(0, -50px);
  animation-timing-function: cubic-bezier(1.000, 0.000, 0.000, 1.000);
  animation: text-anim 4s infinite;
}

/* Solid text on hover */
.perspective-line:hover p{
  color: #eeeeee;
}
  
@keyframes text-anim {
  0% {
    transform: translate(0, 0);
  }
    50% {
    transform: translate(0, -50px);
  }
  100% {
    transform: translate(0, 0);
  }
}
</style>
<div class="perspective-text">
  <div class="perspective-line">
    <p>one stop</p>
    <p>shop</p>
  </div>
  <div class="perspective-line">
    <p>shop</p>
    <p>for all</p>
  </div>
  <div class="perspective-line">
    <p>for all</p>
    <p>electronics</p>
  </div>
</div>