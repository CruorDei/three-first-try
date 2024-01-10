import React, { useEffect, useState } from 'react';
import { Canvas } from 'react-three-fiber';
import { Box, OrbitControls } from '@react-three/drei';

function App() {

  const [boxes, setBoxes] = useState([]);

  useEffect(() => {
    const newBoxes = [];
    const usedPositions = [];

    const isPositionUsed = (pos) =>
      usedPositions.some((usedPos) =>
        usedPos.every((val, i) => val === pos[i])
      );

    for (let i = 0; i < 6; i++) {
      let position;
      do {
        position = [
          Math.floor(Math.random() * 3) - 1, // x
          Math.floor(Math.random() * 3) - 1, // y
          Math.floor(Math.random() * 3) - 1, // z
        ];
      } while (isPositionUsed(position));

      newBoxes.push({ position });
      usedPositions.push(position);
    }

    setBoxes(newBoxes);
  }, []);
  

  return (
    <div style={{ height: "100vh", backgroundColor: "#202020"}}>
    <div></div>
      <Canvas>
      <ambientLight intensity={0.5} />
      <pointLight color="white" position={[10, 10, 10]} />
      <OrbitControls />
      {boxes.map((box, index) => (
        <Box key={index} args={[1, 1, 1]} position={box.position} receiveShadow castShadow>
          <meshStandardMaterial color={'#555'}  />
        </Box>

        
      ))}
      </Canvas>
    </div>
  );
}

export default App;

