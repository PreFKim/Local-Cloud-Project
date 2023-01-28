        function php_func(url,params) {
				var form = document.createElement("form");
				form.setAttribute("method", "post");
				form.setAttribute("action", url);

				for (var key in params) {
					var hiddenField = document.createElement('input');
					hiddenField.setAttribute('type', 'hidden');
					hiddenField.setAttribute('name', key);
					hiddenField.setAttribute('value', params[key]);
					form.appendChild(hiddenField);
				}

				document.body.appendChild(form);
				
				form.submit();
			}

			function deleteobj(id)
			{
				document.getElementById(id).remove();
			}

			function changedir(dirname)
			{
				php_func('<?=$filectrl?>chdir.php',{dir:dirname});
			}

			function deletefile(filename)
			{
				if (confirm(filename+`을 삭제하시겠습니까? \n삭제하면 되돌릴 수 없습니다.`)) 
					php_func("<?=$filectrl?>delete.php",{dir:filename});
			}

			function renamefile(dirname,oldname,newname)
			{
				if (newname == "") alert('파일 이름을 지정해주세요.');
				else if (newname == oldname) alert('기존의 파일명과 동일합니다.');
				else
				{
					if (confirm("파일 이름을 "+oldname+"에서 " + newname +"으로 바꾸시겠습니까?")) 
						php_func("<?=$filectrl?>rename.php",{dir:dirname,old:oldname,new:newname});
				}
			}

			
			function createdir(dirname)
			{
				if (dirname == "")
				{
					alert("폴더명을 입력해주세요.");
				}	
				else
				{
					php_func("<?=$filectrl?>newdir.php",{dir:'<?=$dir?>'+dirname});
				}
			}



			function searchfile(startdir,targetname)
			{
				if (targetname == "")
				{
					alert("찾을 이름을 입력하세요.");
				}
				else
				{
					changedir('search:'+startdir+':'+targetname);
				}
			}

			function downloadfile(filename)
			{
				php_func('<?=$filectrl?>download.php',{dir:filename});
			}

			function uploadfile()
			{
				if (document.getElementById("newfile").files.length == 0) alert("파일을 첨부해주세요");
				else 
				{
					if (confirm("업로드할 파일명과 같은 파일명이 있을 경우 덮어쓰게 됩니다.")) 
						document.getElementById("upload").submit();
				}
			}
			
			function startsetting(searchmode,headerid)
			{
				if (searchmode=="true")
				{
					deleteobj("createfile");
					deleteobj("createdir");
					deleteobj("search");
				}
				document.getElementById('header-'+headerid).className += " active";
			}