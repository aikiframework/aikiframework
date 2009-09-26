(function($)
{  
 	$.fn.autocomplete = function(options) 
 	{  
 		
 			var defaults = {  
					sDiv: "more",
					sFile: "",
					pImage: "loader.gif",
					pImageTxt: "",
					vDest:"",
					dValue:"",
					pGrp:"",
					aPage:"",
					nFile:"",
					sMode:"classic"
			};  

			var options = $.extend(defaults, options); 
			tObj=$(this).attr("id");			
			
			// ----------------------------------------------------------------------------------------------------
			// sFile
			//
			// if neq ""
			//		read xml configFile
			//
			// ----------------------------------------------------------------------------------------------------
			
				if ( options.sFile != "")
				{
						$.get(
									"autocomplete.xml",
									function (xml) 
									{
											$("config",xml).each( 
																					function(i) 
																					{
																							x=$(this).find("autocompletepage").text();
																							options.aPage=x;
																					}
																			);
									}
								);				
				}
				else
				{
						options.aPage=options.nFile;
				}	
				
			// ----------------------------------------------------------------------------------------------------
			// internal Functions
			// ----------------------------------------------------------------------------------------------------

			function html_entity_decode(t) 
			{
				t = t.replace(/&quot;/g,'"');
				t = t.replace(/&amp;/g,'&');
				t = t.replace(/&#39;/g,"'");
				t = t.replace(/&lt;/g,'<');
				t = t.replace(/&gt;/g,'>');
				t = t.replace(/&circ;/g,'^');
				t = t.replace(/&lsquo;/g,'ë');
				t = t.replace(/&rsquo;/g,'í');
				t = t.replace(/&ldquo;/g,'ì');
				t = t.replace(/&rdquo;/g,'î');
				t = t.replace(/&bull;/g,'ï');
				t = t.replace(/&ndash;/g,'ñ');
				t = t.replace(/&mdash;/g,'ó');
				t = t.replace(/&tilde;/g,'ò'); 
				t = t.replace(/&trade;/g,'ô'); 
				t = t.replace(/&scaron;/g,'ö');
				t = t.replace(/&rsaquo;/g,'õ'); 
				t = t.replace(/&oelig;/g,'ú');
				t = t.replace(/&#357;/g,'ù');
				t = t.replace(/&#382;/g,'û');
				t = t.replace(/&Yuml;/g,'ü');
				t = t.replace(/&nbsp;/g,' ');
				t = t.replace(/&iexcl;/g,'°');
				t = t.replace(/&cent;/g,'¢');
				t = t.replace(/&pound;/g,'£');
				t = t.replace(/&curren;/g,' ');
				t = t.replace(/&yen;/g,'•');
				t = t.replace(/&brvbar;/g,'¶');
				t = t.replace(/&sect;/g,'ß');
				t = t.replace(/&uml;/g,'®');
				t = t.replace(/&copy;/g,'©');
				t = t.replace(/&ordf;/g,'™');
				t = t.replace(/&laquo;/g,'´');
				t = t.replace(/&not;/g,'¨'); 
				t = t.replace(/&shy;/g,'≠'); 
				t = t.replace(/&reg;/g,'Æ');
				t = t.replace(/&macr;/g,'Ø');
				t = t.replace(/&deg;/g,'∞');
				t = t.replace(/&plusmn;/g,'±');
				t = t.replace(/&sup2;/g,'≤'); 
				t = t.replace(/&sup3;/g,'≥'); 
				t = t.replace(/&acute;/g,'¥');
				t = t.replace(/&micro;/g,'µ');
				t = t.replace(/&para/g,'∂'); 
				t = t.replace(/&middot;/g,'∑');
				t = t.replace(/&cedil;/g,'∏');
				t = t.replace(/&sup1;/g,'π');
				t = t.replace(/&ordm;/g,'∫');
				t = t.replace(/&raquo;/g,'ª');
				t = t.replace(/&frac14;/g,'º');
				t = t.replace(/&frac12;/g,'Ω');
				t = t.replace(/&frac34;/g,'æ');
				t = t.replace(/&iquest;/g,'ø');
				t = t.replace(/&Agrave;/g,'¿'); 
				t = t.replace(/&Aacute;/g,'¡');
				t = t.replace(/&Acirc;/g,'¬');
				t = t.replace(/&Atilde;/g,'√');
				t = t.replace(/&Auml;/g,'ƒ'); 
				t = t.replace(/&Aring;/g,'≈'); 
				t = t.replace(/&AElig;/g,'∆');
				t = t.replace(/&Ccedil;/g,'«');
				t = t.replace(/&Egrave;/g,'»'); 
				t = t.replace(/&Eacute;/g,'…'); 
				t = t.replace(/&Ecirc;/g,' ');
				t = t.replace(/&Euml;/g,'À');
				t = t.replace(/&Igrave;/g,'Ã');
				t = t.replace(/&Iacute;/g,'Õ');
				t = t.replace(/&Icirc;/g,'Œ'); 
				t = t.replace(/&Iuml;/g,'œ');
				t = t.replace(/&ETH;/g,'–'); 
				t = t.replace(/&Ntilde;/g,'—');
				t = t.replace(/&Ograve;/g,'“');
				t = t.replace(/&Oacute;/g,'”');
				t = t.replace(/&Ocirc;/g,'‘');
				t = t.replace(/&Otilde;/g,'’');
				t = t.replace(/&Ouml;/g,'÷');
				t = t.replace(/&times;/g,'◊');
				t = t.replace(/&Oslash;/g,'ÿ');
				t = t.replace(/&Ugrave;/g,'Ÿ');
				t = t.replace(/&Uacute;/g,'⁄');
				t = t.replace(/&Ucirc;/g,'€');
				t = t.replace(/&Uuml;/g,'‹');
				t = t.replace(/&Yacute;/g,'›');
				t = t.replace(/&THORN;/g,'ﬁ');
				t = t.replace(/&szlig;/g,'ﬂ'); 
				t = t.replace(/&agrave;/g,'‡'); 
				t = t.replace(/&aacute;/g,'·'); 
				t = t.replace(/&acirc;/g,'‚'); 
				t = t.replace(/&atilde;/g,'„'); 
				t = t.replace(/&auml;/g,'‰'); 
				t = t.replace(/&aring;/g,'Â'); 
				t = t.replace(/&aelig;/g,'Ê');
				t = t.replace(/&ccedil;/g,'Á');
				t = t.replace(/&egrave;/g,'Ë');
				t = t.replace(/&eacute;/g,'È');
				t = t.replace(/&ecirc;/g,'Í'); 
				t = t.replace(/&euml;/g,'Î'); 
				t = t.replace(/&igrave;/g,'Ï');
				t = t.replace(/&iacute;/g,'Ì');
				t = t.replace(/&icirc;/g,'Ó'); 
				t = t.replace(/&iuml;/g,'Ô'); 
				t = t.replace(/&eth;/g,'');
				t = t.replace(/&ntilde;/g,'Ò');
				t = t.replace(/&ograve;/g,'Ú');
				t = t.replace(/&oacute;/g,'Û');
				t = t.replace(/&ocirc;/g,'Ù'); 
				t = t.replace(/&otilde;/g,'ı');
				t = t.replace(/&ouml;/g,'ˆ'); 
				t = t.replace(/&divide;/g,'˜'); 
				t = t.replace(/&oslash;/g,'¯');
				t = t.replace(/&ugrave;/g,'˘');
				t = t.replace(/&uacute;/g,'˙');
				t = t.replace(/&ucirc;/g,'˚'); 
				t = t.replace(/&uuml;/g,'¸'); 
				t = t.replace(/&yacute;/g,'˝');
				t = t.replace(/&thorn;/g,'˛');
				t = t.replace(/&yuml;/g,'ˇ');
	
				return t;
			}			
			
			function returnDoAction(myK)
			{
					var d =0;
					if ( myK==37 ) d=1;
					if ( myK==38 ) d=1;
					if ( myK==39 ) d=1;
					if ( myK==40 ) d=1;	
					
					return d;			
			}
			
			// ----------------------------------------------------------------------------------------------------
			// init
			// ----------------------------------------------------------------------------------------------------
			
					// get Number of autocomplete Div
					
					tDiv=$(".autocompletePreload").length;
					options.sDiv="autocomplete"+tDiv;
					options.pGrp="autocomplete"+tDiv;
					
					// Get Id
														
							myId=$(this).attr("id");
							
					// Set Value
					
							$("#"+myId).attr("value",options.dValue);
							$("#"+myId).attr("grp",options.pGrp);
														
					// Generate AutoComplete Div & insert after INPUT

							myStr="";							
							myStr=myStr+"<div class='autocompleteBox' id='"+options.sDiv+"' style='display: none;' parentElm='"+myId+"' grp='"+options.pGrp+"'>";
									myStr=myStr+"<div class='autocompleteList' id='auto"+options.sDiv+"List' grp='"+options.pGrp+"'>";
											myStr=myStr+"<li target='"+myId+"' dValue='' id='"+myId+"li' grp='"+options.pGrp+"'>xx</li>";
									myStr=myStr+"</div>";
							myStr=myStr+"</div>";
							myStr=myStr+"<div class='autocompletePreload' id='"+options.sDiv+"Preload' style='display: none;'><img src='"+options.pImage+"' align='center'>"+options.pImageTxt+"</div>";
														
							$("#"+myId).after(myStr);
														
					// BackUp Box maxHeight;
														
							mh=parseInt( $("#"+options.sDiv).css("height") );
							$("#"+myId).attr("maxHeight",mh);
							$("#"+myId).attr("contentDiv",options.sDiv);			
			
			// ----------------------------------------------------------------------------------------------------
			// mouseOut
			//
			// if
			//		target & src are on the same group ok
			// else
			//		hide autocomplete Div		
			//
			// ----------------------------------------------------------------------------------------------------

							$("#"+tObj+", #"+options.sDiv).mouseout
							(
									function(e)
									{
											var targ;
											if (!e) var e = window.event;
											if (e.target) targ = e.target;
											else if (e.srcElement) targ = e.srcElement;
											if (targ.nodeType == 3)  targ = targ.parentNode;
											
											if (!e) var e = window.event;
											var relTarg = e.relatedTarget || e.fromElement;
											
											targ_id="";
											grp1="";
											try
											{
												grp1=$("#"+targ.id).attr("grp");
												targ_id=targ.id+"("+grp+")";
											}
											catch(e)
											{
												
											}

											relTarg_id="";
											grp2="";
											try
											{
												grp2=$("#"+relTarg.id).attr("grp");
												relTarg_id=relTarg.id+"("+grp+")";
											}
											catch(e)
											{
												
											}
											
											if ( grp1!=grp2 )
											{
												gB=grp1;
												if ( grp2!=undefined )
												{
													gB=grp2;
												}
												//Fix By Bassel Khartabil 7-2-2009
												//No more out of foucs
												//$("#"+gB).hide();
												//$("body").focus();
											}

									}
							)
										
			// ----------------------------------------------------------------------------------------------------
			// focus
			// 
			// if 
			//		element already content something else than "xx" in the first LI then Load
			// else
			//		show div
			//
			// ----------------------------------------------------------------------------------------------------
    		
    					$("#"+tObj).focus
    					( 
    								function (e)
    								{    								    					
    										v=$(this).attr("value")
    										c=$(this).attr("contentDiv");
    					
    										if (v!="")
    										{
    												hh=$("#"+c+" .autocompleteList li:first").html();
    												if (hh!="xx")
    												{
    														$("#"+c).show();
    												}
    												else
    												{
    														myId=this.id
    														$("#"+myId).trigger("keyup");    							
    												}    						
    										}
    										else
    										{
    												myId=this.id
    												$("#"+myId).trigger("keyup");    							    						
    										}    				
									}
					)		
							
			// ----------------------------------------------------------------------------------------------------
			// keyUP
			//
			// Ajax Request
			//
			// ----------------------------------------------------------------------------------------------------
    		
    		$("#"+tObj).keyup
    		( 
    			function (event)
    			{
    					// Vars 
						
								// Position & Win Vars
								
								var_myScroll = $(window).scrollTop(); 
								var_myWindowHeight=$(window).height();
								var_myDocumentHeight=$(document).height();

								p=new Array();
								zz=$("#"+myId).position();
								p[0]=zz.left;
								p[1]=zz.top;										
								addOn=$("#"+myId ).outerHeight();

								// Various
								
								myId=this.id
    							myValue=this.value;

    							mySuggestion=$("#"+myId).attr("contentDiv");
    							myAutoSuggestionList="auto"+$("#"+myId).attr("contentDiv")+"List";
    							
    							inputString=myValue;
					
						// If we got a arrow Key we do nothing
						
								d=0;
								try
								{
									d=returnDoAction(event.keyCode);									
								}
								catch(e)
								{
							
								}
								
								if (d==1) return false;
    					    																	
						
						// If inputString containt something
						
						if(inputString.length == 0) 
						{
								$("#"+mySuggestion).hide();
						} 
						else 
						{      				
								$("#"+mySuggestion+"Preload").css("position","absolute");
								$("#"+mySuggestion+"Preload").css("left",p[0]);
								$("#"+mySuggestion+"Preload").css("top",parseInt(p[1])+addOn);						
								$("#"+mySuggestion+"Preload").show();
								
								options.aPage
								$.post(options.aPage, {queryString: ""+inputString+"", mystring: ""+options.sFile+"" , sMode: ""+options.sMode+"" }, 
								// $.post("autocomplete.php", {queryString: ""+inputString+"", mystring: ""+options.sFile+"" }, 
									function(data)
									{
											$("#"+mySuggestion).hide();											
											$("#"+mySuggestion+"Preload").hide();

											if(data.length >0) 
											{

												// Convert String To Array

														myString = new String(data)
														splitString = myString.split(",")
												
												// Suggestions Values
												
														$("#"+mySuggestion).css("position","absolute");
														$("#"+mySuggestion).css("left",p[0]);
														$("#"+mySuggestion).css("top",parseInt(p[1])+addOn);
														$("#"+mySuggestion).css("height",$("#"+myId).attr("maxHeight"));
														$("#"+mySuggestion).show();
												
												// Clone LI and fill with array values
														
														elm=$("#"+myAutoSuggestionList+" li:first").clone(true);
														$("#"+myAutoSuggestionList).empty();

														th=0;
														for(i=0;i<splitString.length-1;i++)
														{
															elm.appendTo("#"+myAutoSuggestionList);
															
															// If one dest Elm is define
															if ( options.vDest!="")
															{
																myString2 = new String(splitString[i])
																splitString2 = myString2.split("=")																
																$("#"+myAutoSuggestionList+" li:last").attr("dValue",splitString2[0]);
																splitString[i]=splitString2[1];
															}
															
															elm=$("#"+myAutoSuggestionList+" li:last").clone(true);
															
															if ( options.sMode!="classic" )
															{
																myString = new String(splitString[i])
																splitString[i] = myString.replace(inputString, "<b>"+inputString+"</b>","i");
															}
															
															$("#"+myAutoSuggestionList+" li:last").html(splitString[i]);
															th=th+$("#"+myAutoSuggestionList+" li:last").outerHeight();
														}
														
												// ReCss main Div
														
														mh=$("#"+myId).attr("maxHeight");

														if ( th<mh)
														{
																np=th;
																$("#"+mySuggestion).css("overflow-y","hidden")	
																$("#"+mySuggestion).css("height",th+"px")																	
														}
														else												
														{
																np=mh;
																$("#"+mySuggestion).css("overflow-y","auto")	
																$("#"+mySuggestion).css("height",mh+"px")																	
														}																											
												
												// Reposition autocomplete Up if no enought place down
    													
    													totalH=parseInt(p[1])+parseInt(np);
    													if ( totalH>=var_myDocumentHeight )
    													{
    														newP=p[1]-np;
    														$("#"+mySuggestion).css("top",newP);
    													}
    																									
											}

									}
								);
						}  
					

	    		}
    		)     	
			
			// ----------------------------------------------------------------------------------------------------
			
			// ----------------------------------------------------------------------------------------------------
			// Click On LI
			// ----------------------------------------------------------------------------------------------------
			
    		$("#"+options.sDiv+" .autocompleteList li").click
    		(
    			function()
    			{    				
    						tg=$(this).attr("target");
    						mySuggestion=$("#"+tg).attr("contentDiv");
    				        
    				        //Edit by Bassel Khartabil ( don't replace text but add to it)
    				       // var OldValue = $("#"+tg).val();
    				       // var newValue = $(this).text();

    						//$("#"+tg).val(OldValue+newValue);
    						//$("#"+mySuggestion).fadeOut(500);
    						
    						$("#"+tg).val($(this).text());
    						$("#"+mySuggestion).fadeOut(500);      						    	
    				
    						// If one dest Elm is defined populate it
    						// And populate by it tagName
    				
    						if ( options.vDest!="" )
    						{  
    								//
    								// Check if multi tag replace
    								//
    								
    								if ( options.vDest.charAt(0)=="{" )
    								{
    										dv=$(this).attr("dValue");

											myString = new String(options.vDest)
											r = myString.replace("{", "");
											r = r.replace("}", "");
											
											splitStringDatas = dv.split("~");
											splitStringObject = r.split(",");
											
											for(i=0;i<splitStringDatas.length;i++)
											{
													splitStringDatas[i]=html_entity_decode(splitStringDatas[i]);
													$("#"+splitStringObject[i]).attr("value",splitStringDatas[i]);
											}

    								}
    								else
    								{
    										tName=$("#"+options.vDest).get(0).tagName;    					
    										dv=$(this).attr("dValue");

											switch (tName)
											{
		  											case "INPUT" :
  																				$("#"+options.vDest).attr("value",dv);
   																				break;
  													case "IMG" :
  																				$("#"+options.vDest).attr("src",dv);
   																				break;   							
   													default :
   																				break;
											}    						    					    					    					
    								}
    						}
    			}
    		)    																		

			// ----------------------------------------------------------------------------------------------------    		
	};  
})(jQuery); 
