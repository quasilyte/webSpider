package main 

import (
  "os"
  "net/http"
  "io/ioutil"
)

func main() {
  filename := "response.txt"
  response, requestFail := http.Get(os.Args[1])

  if requestFail != nil {
    ioutil.WriteFile(filename, []byte("$ERROR$"), 0777)
  } else {
    buf, readFail := ioutil.ReadAll(response.Body)
    defer response.Body.Close()

    if readFail != nil {
      ioutil.WriteFile(filename, []byte("$ERROR$"), 0777)
    } else {
      ioutil.WriteFile(filename, buf, 0777)
    }
  }
}
