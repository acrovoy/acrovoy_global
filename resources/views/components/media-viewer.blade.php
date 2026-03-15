<media-viewer id="productViewer"></media-viewer>

<script>
class MediaViewer extends HTMLElement {

constructor() {

super();

this.attachShadow({mode:'open'});

this.shadowRoot.innerHTML = `

<style>

.modal {
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.9);
z-index:1000;
}

.modal-content {
width:100%;
height:100%;
display:flex;
justify-content:center;
align-items:center;
overflow:hidden;
touch-action:none;
}

.close {
position:absolute;
top:10px;
right:20px;
font-size:28px;
cursor:pointer;
color:#fff;
z-index:1010;
}

.arrow {
position:absolute;
top:50%;
transform:translateY(-50%);
font-size:48px;
color:#fff;
cursor:pointer;
z-index:1010;
}

.prev { left:20px; }
.next { right:20px; }

.indicator {
position:absolute;
bottom:20px;
color:#fff;
font-size:18px;
left:50%;
transform:translateX(-50%);
}

img,video,iframe{
max-width:100%;
max-height:100%;
cursor:grab;
}

</style>

<div class="modal">

<span class="close">&times;</span>

<div class="modal-content"></div>

<span class="arrow prev">&#10094;</span>
<span class="arrow next">&#10095;</span>

<div class="indicator"></div>

</div>
`;

this.modal = this.shadowRoot.querySelector('.modal');
this.modalContent = this.shadowRoot.querySelector('.modal-content');
this.closeBtn = this.shadowRoot.querySelector('.close');
this.prevBtn = this.shadowRoot.querySelector('.prev');
this.nextBtn = this.shadowRoot.querySelector('.next');
this.indicator = this.shadowRoot.querySelector('.indicator');

this.currentIndex = 0;
this.mediaArray = [];

this.closeBtn.onclick = () => this.modal.style.display = 'none';
this.modal.onclick = e => { if(e.target === this.modal) this.modal.style.display='none' };

this.prevBtn.onclick = () => this.showMedia(this.currentIndex - 1);
this.nextBtn.onclick = () => this.showMedia(this.currentIndex + 1);

}

open(list,index=0){

this.mediaArray = list;
this.currentIndex = index;

this.showMedia(index);

}

showMedia(index){

if(index < 0) index = this.mediaArray.length-1;
if(index >= this.mediaArray.length) index = 0;

this.currentIndex = index;

const item = this.mediaArray[index];

this.modalContent.innerHTML = '';

this.indicator.textContent = `${index+1} / ${this.mediaArray.length}`;

if(item.type === 'video'){

const video = document.createElement('video');

video.src = item.src;
video.controls = true;
video.autoplay = true;

this.modalContent.appendChild(video);

}

else if(item.type === 'pdf'){

const iframe = document.createElement('iframe');

iframe.src = item.src;
iframe.style.width='80vw';
iframe.style.height='80vh';

this.modalContent.appendChild(iframe);

}

else{

const img = document.createElement('img');

img.src = item.src;

this.addZoomPan(img);
this.addPinchZoom(img);

this.modalContent.appendChild(img);

}

this.modal.style.display='flex';

}

addZoomPan(element){

let scale=1,originX=0,originY=0,startX,startY;

element.onwheel = e => {

e.preventDefault();

scale += e.deltaY * -0.001;

scale = Math.min(Math.max(.5, scale), 5);

element.style.transform=`scale(${scale}) translate(${originX}px,${originY}px)`;

};

element.onmousedown = e => {

startX = e.clientX-originX;
startY = e.clientY-originY;

const move = ev => {

originX = ev.clientX-startX;
originY = ev.clientY-startY;

element.style.transform=`scale(${scale}) translate(${originX}px,${originY}px)`;

};

const up = () => {

window.removeEventListener('mousemove',move);
window.removeEventListener('mouseup',up);

};

window.addEventListener('mousemove',move);
window.addEventListener('mouseup',up);

};

}

addPinchZoom(element){

let initialDistance=null,scale=1;

element.addEventListener('touchmove',e=>{

if(e.touches.length===2){

e.preventDefault();

const [t1,t2]=e.touches;

const dx=t2.clientX-t1.clientX;
const dy=t2.clientY-t1.clientY;

const distance=Math.hypot(dx,dy);

if(!initialDistance) initialDistance=distance;

const newScale = scale * distance / initialDistance;

element.style.transform=`scale(${Math.min(Math.max(.5,newScale),5)})`;

}

},{passive:false});

}

}

customElements.define('media-viewer',MediaViewer);
</script>