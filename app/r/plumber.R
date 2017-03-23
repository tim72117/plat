library(plumber)
r <- plumb("analysis.R")
r$run(port=8000)


