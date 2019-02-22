function pageChanged(){
  document.querySelectorAll('pre code').forEach((block) => {
      hljs.highlightBlock(block);
      block.innerHTML = "<ol><li>" + block.innerHTML.replace(/\n/g,"\n</li><li>")+"\n</li></ol>"
  });

  if ( document.getElementById("comments") && typeof(gitm_config) != "undefined" ) {
      var gm = new GitM(gitm_config)
      gm.render('comments')   
  }
}