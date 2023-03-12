<script>
let videoList = document.querySelectorAll('.video-list-container .list');
let mainVideoList = document.querySelectorAll('.main-video-container .main-video .vid');

videoList.forEach(vid =>{
   vid.onclick = () =>{
      const index = [...vid.parentNode.children].indexOf(vid);
      videoList.forEach(remove =>{remove.classList.remove('active')});
      mainVideoList.forEach(node =>{node.style.display = 'none'});
      vid.classList.add('active');
      let mainVid = mainVideoList[index];
      mainVid.style.display = 'inline';
     let title = vid.querySelector('.list-title').innerHTML;
     document.querySelector('.main-video-container .main-vid-title').innerHTML = title;
   };
});
</script>
