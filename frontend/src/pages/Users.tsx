import axios from 'axios'
import React from 'react'

import { useEffect } from 'react'

function Users() {


  useEffect(() => {
    axios.get("http://localhost:8080/v1/person")
      .then((response) => console.log(response.data))
      .catch((error) => console.error("Error:", error));
  }, []);

  return (
    <>
    <div>Users</div>
    
    </>
  )
}

export default Users