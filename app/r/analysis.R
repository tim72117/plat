#* @get /reg
reg <- function(sql,r){
  library(RODBC)
  #conn_1 = odbcConnect(dsn = "plat", uid = "funk6478") #連線至plat測試機
  #sql_1 = paste0("select id,tablename from [file_analysis] where id = ",tableid) #從plat撈取tablename的sql語法
  #data_1 = sqlQuery(conn_1, sql_1) #執行sql_1
  #data_1$tablename[1] #取得tablename
  conn_2 <- odbcDriverConnect('driver={SQL Server};server=192.168.1.99;database=analysis_data;trusted_connection=true;UID=uid;PWD=pwd')
  #conn_2 = odbcConnect(dsn = "tted_analysis", uid = "funk6478") #連線至線上分析資料
  #sql_2 = paste0("select ",columns1,",",columns2," from ","[",data_1$tablename[1],"]") #從線上分析資料撈取欄位的sql語法
  data_2 = sqlQuery(conn_2, sql) #執行sql_2
  #columns1_r = c(gsub(",","+",columns1)) #將逗號替換成加號
  #GGG = paste0(columns2,"~",columns1_r)
  library(car)
  library(MatrixModels)
  library(lme4)
  model_lm = lm(r, data_2)
  model_lm['coefficients']
  model_lm_summary = summary(model_lm)
  model_lm_list1 = data.frame(
                              model_lm_summary$coefficients
                             )
  model_lm_list1_r = c('Estimate', 'Std. Error', 't. value', 'Pr(>|t|)')
  variables = as.character(c('variables',row.names(model_lm_summary$coefficients)))
  model_lm_list1_r1 = rbind(model_lm_list1_r,model_lm_list1)
  model_lm_list1_rc = cbind(variables,model_lm_list1_r1)
  model_lm_list2 = data.frame(
                              R_squared = c('r.squared',model_lm_summary$r.squared),
                              adj_R_squared = c('adj.r.squared',model_lm_summary$adj.r.squared)
                             )
  c(model_lm_list1_rc,model_lm_list2)
}

#* @get /cor
cor_R <- function(sql){
  library(RODBC)
  #conn_1c <- odbcDriverConnect('driver={SQL Server};server=192.168.1.99;database=analysis_data;trusted_connection=true;UID=uid;PWD=pwd')
  #conn_1c = odbcConnect(dsn = "plat", uid = "funk6478") #連線至plat測試機
  #sql_1c = paste0("select id,tablename from plat_use.dbo.[file_analysis] where id = ", tableid) #從plat撈取tablename的sql語法
  #sql_1c
  #data_1c = sqlQuery(conn_1c, sql_1c) #執行sql_1c
  #data_1c$tablename[1] #取得tablename
  conn_2c <- odbcDriverConnect('driver={SQL Server};server=192.168.1.99;database=analysis_data;trusted_connection=true;UID=uid;PWD=pwd')
  #conn_2c = odbcConnect(dsn = "tted_analysis", uid = "funk6478") #連線至線上分析資料
  #sql_2c = paste0("select ",columns1,",",columns2," from ","[",data_1c$tablename[1],"] where ",columns1,"<>'' and ",columns2, "<>'' and ",columns1, "<>-9 and ",columns2, "<>-9 and ",columns1, "<>-8 and ",columns2, "<>-8") #從線上分析資料撈取欄位的sql語法
  #sql_2c

  data_2c = sqlQuery(conn_2c, sql) #執行sql_2c
  data_2c_na = data.frame(na.omit(data_2c))
  # #data_2c_na
  cor_data_c = cor(data_2c_na)
  # #cor_data_c
}