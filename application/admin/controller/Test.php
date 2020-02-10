<?php

public ModelMap getBase64(HttpServletRequest request,HttpServletResponse response)	{
		 ModelMap result = new ModelMap();
	        // 声明一个工作薄
	        XSSFWorkbook wb = new XSSFWorkbook();
	        // 生成一个样式  
	        XSSFCellStyle style = wb.createCellStyle();
	        //样式字体居中
	        style.setAlignment(HSSFCellStyle.ALIGN_CENTER);
	        
	        //声明第一个sheet名称
	        XSSFSheet sheet1 = wb.createSheet("问题道路图形展示");
 
		List<byte[]> images = new ArrayList<>();
				
		// 必须去除每个base64前面的"data:image/png;base64,"这22个字符
		try {
			images.add(decode(request.getParameter("imagesBase64" ).substring(22)));
		} catch (IOException e) {
			e.printStackTrace();
		}
					
					//设置宽高
					  sheet1.setDefaultRowHeight((short)(350*30/25));
				          sheet1.setColumnWidth( (int)(400*1990/140));
					
					  //将获取到的base64 编码转换成图片，画到excel中
					  XSSFDrawing patriarch  =sheet1.createDrawingPatriarch();
						XSSFClientAnchor anchor=null;
						int index=0;
						for(byte[] image:images){
							anchor = new XSSFClientAnchor(0,0,0,0,(short) (8*(index%3)),((index/3)*18),(short)(7+8*(index%3)),									16+((index/3)*18)); 
							patriarch.createPicture(anchor, wb.addPicture(image, XSSFWorkbook.PICTURE_TYPE_PNG));
							index++;
						}
						// 工程路径（根据个人需要，路径可以写简单的磁盘路径）
						String parentPath = (new File(getClass().getResource("/").getFile()
								.toString())).getParentFile().getParent();
						String path = request.getContextPath();
						String fileName = parentPath + File.separator + "csvfile"
								+ File.separator + "等级道路数据导出"+startTime+".xlsx";
						File file = new File(fileName);
						if (file.isFile()) {
							file.delete();
						}
						
						// 规定的路径下，生成excel表格
						FileOutputStream output = null;
						try {
							output = new FileOutputStream(fileName);
							wb.write(output);
							output.flush();
							output.close();
							result.put("filePath", path + File.separator + "csvfile"
									+ File.separator + "等级道路数据导出"+startTime+".xlsx");
							result.put("success", 1);
						}catch (FileNotFoundException e) {
								result.put("success", 0);
								e.printStackTrace();
							} catch (IOException e) {
								result.put("success", -1);
								try {
									if (wb != null) {
										wb.close();
									}
									if (output != null) {
										output.close();
									}
								} catch (Exception e1) {
									e1.printStackTrace();
								}
							}
	
	       	return result;//返回excel的路径到js中的window.location.herf ，触发浏览器的下载功能
}


?>